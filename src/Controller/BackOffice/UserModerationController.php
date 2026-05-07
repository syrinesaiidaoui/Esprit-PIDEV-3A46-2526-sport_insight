<?php

namespace App\Controller\BackOffice;

use App\Entity\Commentaire;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\CommentaireRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users-moderation')]
#[IsGranted('ROLE_ADMIN')]
class UserModerationController extends AbstractController
{
    private const ALLOWED_ROLES = [
        'ROLE_USER',
        'ROLE_JOUEUR',
        'ROLE_ENTRAINEUR',
        'ROLE_ADMIN',
    ];

    #[Route('', name: 'admin_user_moderation_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        MessageRepository $messageRepository,
        CommentaireRepository $commentaireRepository
    ): Response {
        $users = $userRepository->createQueryBuilder('u')
            ->orderBy('u.dateInscription', 'DESC')
            ->addOrderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();

        $invitations = $messageRepository->createQueryBuilder('m')
            ->leftJoin('m.sender', 'sender')
            ->leftJoin('m.receiver', 'receiver')
            ->addSelect('sender', 'receiver')
            ->orderBy('m.sentAt', 'DESC')
            ->setMaxResults(40)
            ->getQuery()
            ->getResult();

        $commentaires = $commentaireRepository->findBy([], ['dateCommentaire' => 'DESC'], 40);

        $userPrimaryRoles = [];
        foreach ($users as $user) {
            if (!$user instanceof User || $user->getId() === null) {
                continue;
            }
            $userPrimaryRoles[$user->getId()] = $this->resolvePrimaryRole($user);
        }

        return $this->render('back_office/user_moderation/index.html.twig', [
            'users' => $users,
            'invitations' => $invitations,
            'commentaires' => $commentaires,
            'allowedRoles' => self::ALLOWED_ROLES,
            'userPrimaryRoles' => $userPrimaryRoles,
        ]);
    }

    #[Route('/{id}/role', name: 'admin_user_moderation_role', methods: ['POST'])]
    public function updateRole(Request $request, User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('change_role_' . $user->getId(), $token)) {
            $this->addFlash('danger', 'Invalid CSRF token for role update.');
            return $this->redirectToRoute('admin_user_moderation_index');
        }

        $targetRole = strtoupper(trim((string) $request->request->get('role', 'ROLE_USER')));
        if (!in_array($targetRole, self::ALLOWED_ROLES, true)) {
            $this->addFlash('danger', 'Selected role is not allowed.');
            return $this->redirectToRoute('admin_user_moderation_index');
        }

        $currentUser = $this->getUser();
        if ($currentUser instanceof User && $currentUser->getId() === $user->getId() && $targetRole !== 'ROLE_ADMIN') {
            $this->addFlash('warning', 'You cannot remove your own admin role.');
            return $this->redirectToRoute('admin_user_moderation_index');
        }

        $user->setRoles([$targetRole]);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Role updated for %s.', (string) $user->getEmail()));

        return $this->redirectToRoute('admin_user_moderation_index');
    }

    #[Route('/invitations/{id}/delete', name: 'admin_user_moderation_invitation_delete', methods: ['POST'])]
    public function deleteInvitation(Request $request, Message $message, EntityManagerInterface $entityManager): RedirectResponse
    {
        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('delete_invitation_' . $message->getId(), $token)) {
            $this->addFlash('danger', 'Invalid CSRF token for invitation deletion.');
            return $this->redirectToRoute('admin_user_moderation_index');
        }

        $entityManager->remove($message);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation removed successfully.');

        return $this->redirectToRoute('admin_user_moderation_index');
    }

    #[Route('/commentaires/{id}/delete', name: 'admin_user_moderation_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): RedirectResponse
    {
        $token = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('delete_comment_moderation_' . $commentaire->getId(), $token)) {
            $this->addFlash('danger', 'Invalid CSRF token for comment deletion.');
            return $this->redirectToRoute('admin_user_moderation_index');
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Comment removed successfully.');

        return $this->redirectToRoute('admin_user_moderation_index');
    }

    private function resolvePrimaryRole(User $user): string
    {
        $roles = $user->getRoles();
        $priority = ['ROLE_ADMIN', 'ROLE_ENTRAINEUR', 'ROLE_JOUEUR', 'ROLE_USER'];

        foreach ($priority as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return 'ROLE_USER';
    }
}
