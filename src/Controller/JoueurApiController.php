<?php

namespace App\Controller;

use App\Repository\JoueurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class JoueurApiController extends AbstractController
{
    #[Route('/joueurs/{equipeId}', name: 'api_joueurs_by_equipe', methods: ['GET'])]
    public function getJoueursByEquipe(int $equipeId, JoueurRepository $joueurRepository): JsonResponse
    {
        $joueurs = $joueurRepository->findByEquipe($equipeId);
        
        $data = array_map(function($joueur) {
            return [
                'id' => $joueur->getId(),
                'nom' => $joueur->getNom(),
                'prenom' => $joueur->getPrenom(),
                'numero' => $joueur->getNumero(),
                'label' => sprintf('%d - %s %s', $joueur->getNumero(), $joueur->getNom(), $joueur->getPrenom()),
            ];
        }, $joueurs);
        
        return new JsonResponse($data);
    }
}
