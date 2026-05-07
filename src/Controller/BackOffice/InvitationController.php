<?php

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class InvitationController extends AbstractController
{
    #[Route('/admin/invitations', name: 'back_office_invitation_index', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('admin_user_moderation_index');
    }
}
