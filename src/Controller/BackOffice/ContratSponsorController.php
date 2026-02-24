<?php

namespace App\Controller\BackOffice;

use App\Entity\ContratSponsor;
use App\Form\ContratSponsorType;
use App\Repository\ContratSponsorRepository;
use App\Service\CoachNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/contrat/sponsor')]
final class ContratSponsorController extends AbstractController
{
    #[Route(name: 'app_contrat_sponsor_index', methods: ['GET'])]
    public function index(Request $request, ContratSponsorRepository $contratSponsorRepository, EntityManagerInterface $entityManager, CoachNotificationService $notificationService): Response
    {
        $sponsorNom = $request->query->get('sponsor_nom');
        $dateDebut = $request->query->get('date_debut');

        // Convertir la date si elle est fournie
        $dateDebutObj = null;
        if ($dateDebut) {
            try {
                $dateDebutObj = new \DateTime($dateDebut);
            } catch (\Exception $e) {
                $dateDebutObj = null;
            }
        }

        // Rechercher avec les critères
        if ($sponsorNom || $dateDebutObj) {
            $contrats = $contratSponsorRepository->searchContrats($sponsorNom, $dateDebutObj);
        } else {
            $contrats = $contratSponsorRepository->findAll();
        }

        // Vérifier les contrats expirés et envoyer les notifications
        foreach ($contrats as $contrat) {
            if ($contrat->isExpired() && !$contrat->isNotified()) {
                $contrat->setStatut('Expiré');
                $contrat->setNotified(true);

                $coach = $contrat->getEquipe()->getEntraineur();
                if ($coach) {
                    $notificationService->notifyCoach(
                        $coach,
                        "Le contrat du sponsor {$contrat->getSponsor()->getNom()} est expiré."
                    );
                }

                $entityManager->persist($contrat);
            }
        }
        $entityManager->flush();
        return $this->render('back_office/contrat_sponsor/index.html.twig', [
            'contrat_sponsors' => $contrats,
            'sponsor_nom' => $sponsorNom,
            'date_debut' => $dateDebut,
        ]);
    }

    #[Route('/new', name: 'app_contrat_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contratSponsor = new ContratSponsor();
        $form = $this->createForm(ContratSponsorType::class, $contratSponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle optional sponsor logo upload from contract form
            $uploadedLogo = $form->get('sponsorLogoFile')->getData();
            if ($uploadedLogo) {
                $sponsor = $contratSponsor->getSponsor();
                if ($sponsor) {
                    $sponsor->setLogoFile($uploadedLogo);
                    // Mettre à jour la date de modification
                    $sponsor->setUpdatedAt(new \DateTimeImmutable());
                    $entityManager->persist($sponsor);
                }
            }

            $entityManager->persist($contratSponsor);
            $entityManager->flush();

            if ($uploadedLogo) {
                $this->addFlash('success', 'Logo téléchargé avec succès');
            }

            $referer = $request->headers->get('referer', '');
            $route = str_contains($referer, '/admin') ? 'back_sponsoring_index' : 'front_sponsoring_index';

            return $this->redirectToRoute($route, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/contrat_sponsor/new.html.twig', [
            'contrat_sponsor' => $contratSponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contrat_sponsor_show', methods: ['GET'])]
    public function show(ContratSponsor $contratSponsor): Response
    {
        return $this->render('back_office/contrat_sponsor/show.html.twig', [
            'contrat_sponsor' => $contratSponsor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contrat_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContratSponsor $contratSponsor, EntityManagerInterface $entityManager): Response
    {
        // Stocker l'ancien nom du logo pour comparaison
        $originalLogoName = $contratSponsor->getSponsor() ? $contratSponsor->getSponsor()->getLogoName() : null;
        
        $form = $this->createForm(ContratSponsorType::class, $contratSponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle optional sponsor logo upload from contract edit form
            $uploadedLogo = $form->get('sponsorLogoFile')->getData();
            if ($uploadedLogo) {
                $sponsor = $contratSponsor->getSponsor();
                if ($sponsor) {
                    $sponsor->setLogoFile($uploadedLogo);
                    // Mettre à jour la date de modification
                    $sponsor->setUpdatedAt(new \DateTimeImmutable());
                    $entityManager->persist($sponsor);
                }
            }

            $entityManager->flush();

            // Vérifier si le logo a été mis à jour
            if ($uploadedLogo && $contratSponsor->getSponsor()) {
                $newLogoName = $contratSponsor->getSponsor()->getLogoName();
                if ($newLogoName && $newLogoName !== $originalLogoName) {
                    $this->addFlash('success', 'Logo mis à jour: ' . $newLogoName);
                }
            }

            $referer = $request->headers->get('referer', '');
            $route = str_contains($referer, '/admin') ? 'back_sponsoring_index' : 'front_sponsoring_index';

            return $this->redirectToRoute($route, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/contrat_sponsor/edit.html.twig', [
            'contrat_sponsor' => $contratSponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/pdf', name: 'app_contrat_sponsor_pdf', methods: ['GET'])]
    public function pdf(ContratSponsor $contratSponsor): Response
    {
        return $this->render('back_office/contrat_sponsor/pdf.html.twig', [
            'contrat_sponsor' => $contratSponsor,
        ]);
    }

    #[Route('/{id}', name: 'app_contrat_sponsor_delete', methods: ['POST'])]
    public function delete(Request $request, ContratSponsor $contratSponsor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contratSponsor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contratSponsor);
            $entityManager->flush();
        }

        $referer = $request->headers->get('referer', '');
        $route = str_contains($referer, '/admin') ? 'back_sponsoring_index' : 'front_sponsoring_index';

        return $this->redirectToRoute($route, [], Response::HTTP_SEE_OTHER);
    }
}
