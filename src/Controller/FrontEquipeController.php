<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/equipes')]
final class FrontEquipeController extends AbstractController
{
    #[Route(name: 'app_front_equipes_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, Request $request): Response
    {
        $sortField = $request->query->get('sort', 'nom');
        $sortOrder = $request->query->get('order', 'asc');
        $search = $request->query->get('search', '');

        $qb = $equipeRepository->createQueryBuilder('e');

        // Recherche textuelle : nom uniquement
        if ($search) {
            $qb->andWhere('LOWER(e.nom) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        $equipes = $qb->orderBy('e.' . $sortField, $sortOrder)
            ->getQuery()
            ->getResult();

        return $this->render('front_office/equipes/index.html.twig', [
            'equipes' => $equipes,
            'currentSort' => $sortField,
            'currentOrder' => $sortOrder,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_front_equipes_show', methods: ['GET'])]
    public function show(int $id, EquipeRepository $equipeRepository): Response
    {
        $equipe = $equipeRepository->find($id);

        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }

        return $this->render('front_office/equipes/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }
}
