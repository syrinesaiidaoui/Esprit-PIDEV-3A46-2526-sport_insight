<?php

namespace App\Controller\BackOffice;

use App\Entity\Entrainement;
<<<<<<< HEAD
use App\Form\EntrainementType;
use App\Repository\EntrainementRepository;
=======
use App\Entity\User; // Ajouté pour trouver les joueurs
use App\Form\EntrainementType;
use App\Repository\EntrainementRepository;
use App\Service\NotificationService; // Ajouté pour le mail/notif
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
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
<<<<<<< HEAD
=======
        
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
        if ($sortBy === 'dateEntrainement') {
            $qb->orderBy('e.dateEntrainement', $sortDir === 'desc' ? 'DESC' : 'ASC');
        } else {
            $qb->orderBy('e.id', 'DESC');
        }
<<<<<<< HEAD
=======
        
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
        $entrainements = $qb->getQuery()->getResult();

        return $this->render('back_office/entrainement/index.html.twig', [
            'entrainements' => $entrainements,
            'search_type' => $searchType,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ]);
    }

<<<<<<< HEAD
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
=======
    // UNE SEULE MÉTHODE NEW ICI
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NotificationService $notifier): Response
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    {
        $entrainement = new Entrainement();
        $form = $this->createForm(EntrainementType::class, $entrainement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entrainement);
            $entityManager->flush();

<<<<<<< HEAD
=======
            // 1. Récupérer les joueurs associés à cet entraînement
            $recipients = $entrainement->getJoueurs()->toArray();
            if ($entrainement->getEntraineur()) {
                $recipients[] = $entrainement->getEntraineur();
            }

            $notified = [];
            foreach ($recipients as $joueur) {
                $key = $joueur->getId() ?? spl_object_id($joueur);
                if (isset($notified[$key])) {
                    continue;
                }

                $notifier->notifyPlayerNewTraining($joueur, $entrainement);
                $notified[$key] = true;
            }

            $this->addFlash('success', 'Entraînement créé et notifications envoyées !');
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
            return $this->redirectToRoute('back_entrainement_index');
        }

        return $this->render('back_office/entrainement/new.html.twig', [
<<<<<<< HEAD
            'form' => $form,
=======
            'form' => $form->createView(),
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
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
<<<<<<< HEAD

=======
            $this->addFlash('success', 'Entraînement mis à jour.');
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
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
<<<<<<< HEAD
=======
            $this->addFlash('danger', 'Entraînement supprimé.');
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
        }

        return $this->redirectToRoute('back_entrainement_index');
    }
}
