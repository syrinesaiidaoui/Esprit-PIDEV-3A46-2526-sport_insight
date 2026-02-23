<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/joueurs')]
class FrontJoueurController extends AbstractController
{
    #[Route('', name: 'app_joueurs_index', methods: ['GET'])]
    public function index(JoueurRepository $joueurRepository, EquipeRepository $equipeRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $equipe = $request->query->get('equipe', '');
        $poste = $request->query->get('poste', '');

        $qb = $joueurRepository->createQueryBuilder('j');

        // Recherche textuelle : nom, prénom
        if ($search) {
            $qb->andWhere('LOWER(j.nom) LIKE LOWER(:search) OR LOWER(j.prenom) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par équipe
        if ($equipe) {
            $qb->andWhere('j.equipe = :equipe')
                ->setParameter('equipe', $equipe);
        }

        // Filtre par poste
        if ($poste) {
            $qb->andWhere('LOWER(j.poste) LIKE LOWER(:poste)')
                ->setParameter('poste', '%' . $poste . '%');
        }

        $joueurs = $qb->orderBy('j.equipe', 'ASC')
            ->addOrderBy('j.numero', 'ASC')
            ->getQuery()
            ->getResult();
        
        // Grouper les joueurs par équipe
        $joueursGroupes = [];
        foreach ($joueurs as $joueur) {
            $equipeId = $joueur->getEquipe() ? $joueur->getEquipe()->getId() : null;
            if ($equipeId) {
                if (!isset($joueursGroupes[$equipeId])) {
                    $joueursGroupes[$equipeId] = [
                        'equipe' => $joueur->getEquipe(),
                        'joueurs' => []
                    ];
                }
                $joueursGroupes[$equipeId]['joueurs'][] = $joueur;
            }
        }
        
        $equipes = $equipeRepository->findAll();

        return $this->render('joueur/index.html.twig', [
            'joueursGroupes' => $joueursGroupes,
            'equipes' => $equipes,
            'now' => new \DateTime(),
            'search' => $search,
            'equipe' => $equipe,
            'poste' => $poste,
        ]);
    }

    #[Route('/{id}', name: 'app_joueur_show', methods: ['GET'])]
    public function show(int $id, JoueurRepository $joueurRepository): Response
    {
        $joueur = $joueurRepository->find($id);
        
        if (!$joueur) {
            throw $this->createNotFoundException('Le joueur n\'existe pas');
        }

        return $this->render('joueur/show.html.twig', [
            'joueur' => $joueur,
            'now' => new \DateTime(),
        ]);
    }

    #[Route('/equipe/{equipeId}', name: 'app_joueurs_by_equipe', methods: ['GET'])]
    public function byEquipe(int $equipeId, JoueurRepository $joueurRepository, EquipeRepository $equipeRepository): Response
    {
        $equipe = $equipeRepository->find($equipeId);
        
        if (!$equipe) {
            throw $this->createNotFoundException('L\'équipe n\'existe pas');
        }

        $joueurs = $joueurRepository->findByEquipe($equipeId);

        return $this->render('joueur/by_equipe.html.twig', [
            'equipe' => $equipe,
            'joueurs' => $joueurs,
            'now' => new \DateTime(),
        ]);
    }
}
