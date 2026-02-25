<?php

namespace App\Controller\BackOffice;

use App\Entity\Entrainement;
use App\Entity\User; // Ajouté pour trouver les joueurs
use App\Form\EntrainementType;
use App\Repository\EntrainementRepository;
use App\Service\NotificationService; // Ajouté pour le mail/notif
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/back/entrainement', name: 'back_entrainement_')]
final class EntrainementController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, EntrainementRepository $entrainementRepository): Response
    {
        $searchType = $request->query->get('search_type', '');
        $sortBy = $request->query->get('sort_by', '');
        $sortDir = $request->query->get('sort_dir', 'asc');

        $qb = $entrainementRepository->createQueryBuilder('e');
        if ($searchType) {
            $qb->andWhere('LOWER(e.type) LIKE :searchType')
                ->setParameter('searchType', '%' . strtolower($searchType) . '%');
        }
        
        if ($sortBy === 'dateEntrainement') {
            $qb->orderBy('e.dateEntrainement', $sortDir === 'desc' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('e.id', 'DESC');
        }
        
        $entrainements = $qb->getQuery()->getResult();

        return $this->render('back_office/entrainement/index.html.twig', [
            'entrainements' => $entrainements,
            'search_type' => $searchType,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ]);
    }

    // UNE SEULE MÉTHODE NEW ICI
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NotificationService $notifier): Response
    {
        $entrainement = new Entrainement();
        $form = $this->createForm(EntrainementType::class, $entrainement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entrainement);
            $entityManager->flush();

            // 1. Récupérer les joueurs associés à cet entraînement
            $joueurs = $entrainement->getJoueurs();
            
            foreach ($joueurs as $joueur) {
                $notifier->notifyPlayerNewTraining($joueur, $entrainement);
            }

            $this->addFlash('success', 'Entraînement créé et notifications envoyées !');
            return $this->redirectToRoute('back_entrainement_index');
        }

        return $this->render('back_office/entrainement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Entrainement $entrainement): Response
    {
        return $this->render('back_office/entrainement/show.html.twig', [
            'entrainement' => $entrainement,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entrainement $entrainement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntrainementType::class, $entrainement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Entraînement mis à jour.');
            return $this->redirectToRoute('back_entrainement_index');
        }

        return $this->render('back_office/entrainement/edit.html.twig', [
            'form' => $form->createView(),
            'entrainement' => $entrainement,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Entrainement $entrainement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$entrainement->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($entrainement);
            $entityManager->flush();
            $this->addFlash('danger', 'Entraînement supprimé.');
        }

        return $this->redirectToRoute('back_entrainement_index');
    }
}