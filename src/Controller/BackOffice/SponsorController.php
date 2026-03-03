<?php

namespace App\Controller\BackOffice;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\SponsorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/sponsor')]
final class SponsorController extends AbstractController
{
    #[Route(name: 'app_sponsor_index', methods: ['GET'])]
    public function index(Request $request, SponsorRepository $sponsorRepository): Response
    {
        $email = $request->query->get('email');
        $budget = $request->query->get('budget');
        
        // Convertir le budget en float s'il est fourni
        $budgetFloat = null;
        if ($budget) {
            $budgetFloat = floatval($budget);
        }
        
        // Rechercher avec les critères
        if ($email || $budgetFloat) {
            $sponsors = $sponsorRepository->searchSponsors($email, $budgetFloat);
        } else {
            $sponsors = $sponsorRepository->findAll();
        }
        
        return $this->render('back_office/sponsor/index.html.twig', [
            'sponsors' => $sponsors,
            'email' => $email,
            'budget' => $budget,
        ]);
    }

    #[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If a file was uploaded ensure it's passed to the entity (mapped field usually does this)
            $uploaded = $form->get('logoFile')->getData();
            if ($uploaded) {
                $sponsor->setLogoFile($uploaded);
                $entityManager->persist($sponsor);
            }

            $entityManager->persist($sponsor);
            $entityManager->flush();

            if ($uploaded) {
                $this->addFlash('success', 'Logo téléchargé: ' . $sponsor->getLogoName());
            }

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/sponsor/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(Sponsor $sponsor): Response
    {
        return $this->render('back_office/sponsor/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Stocker l'ancien nom du logo pour comparaison
        $originalLogoName = $sponsor->getLogoName();
        
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploaded = $form->get('logoFile')->getData();
            
            if ($uploaded) {
                // Nouveau fichier téléchargé - le définir pour le traitement par VichUploader
                $sponsor->setLogoFile($uploaded);
                // Mettre à jour la date de modification
                $sponsor->setUpdatedAt(new \DateTimeImmutable());
            }

            // Sauvegarder les modifications
            $entityManager->flush();

            // Vérifier si le logo a été mis à jour
            $newLogoName = $sponsor->getLogoName();
            if ($uploaded && $newLogoName && $newLogoName !== $originalLogoName) {
                $this->addFlash('success', 'Logo mis à jour: ' . $newLogoName);
            }

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back_office/sponsor/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sponsor_delete', methods: ['POST'])]
    public function delete(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sponsor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }
}
