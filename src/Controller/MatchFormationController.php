<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Repository\MatchLineupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/matchs-formation')]
class MatchFormationController extends AbstractController
{
    #[Route('/{id}/edit-formation', name: 'app_match_edit_formation', methods: ['GET'])]
    public function editFormation(Matchs $match): Response
    {
        $domicileLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'domicile')->toArray();
        $exterieurLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'exterieur')->toArray();

        usort($domicileLineups, fn($a, $b) => $a->getJoueur()->getNumero() <=> $b->getJoueur()->getNumero());
        usort($exterieurLineups, fn($a, $b) => $a->getJoueur()->getNumero() <=> $b->getJoueur()->getNumero());

        // pass weather API key from env parameter
        $weatherApiKey = $this->getParameter('app.openweather_api_key');

        return $this->render('front_office/match/formation_editor.html.twig', [
            'match' => $match,
            'domicileLineups' => $domicileLineups,
            'exterieurLineups' => $exterieurLineups,
            'weatherApiKey' => $weatherApiKey,
        ]);
    }

    #[Route('/{id}/save-positions', name: 'app_match_save_positions', methods: ['POST'])]
    public function savePositions(Request $request, Matchs $match, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['positions'])) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['positions'] as $lineupId => $position) {
            $lineup = $match->getMatchLineups()->filter(fn($l) => $l->getId() == $lineupId)->first();
            if ($lineup) {
                $lineup->setPositionX($position['x'] ?? null);
                $lineup->setPositionY($position['y'] ?? null);
            }
        }

        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Positions sauvegardées']);
    }
}
