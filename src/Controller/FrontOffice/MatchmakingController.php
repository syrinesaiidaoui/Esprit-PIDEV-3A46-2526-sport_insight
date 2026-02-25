<?php

namespace App\Controller\FrontOffice;

use App\Service\MatchmakingService;
use App\Service\AiCoachService;
use App\Repository\EvaluationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/matchmaking', name: 'front_matchmaking_')]
class MatchmakingController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        MatchmakingService $matchmakingService, 
        AiCoachService $aiCoachService,
        EvaluationRepository $evaluationRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Obtenir tous les groupes (uniquement pour l'entraîneur connecté)
        $groups = $matchmakingService->generateMatchmakingGroups($user);

        // Obtenir des conseils d'entraîneur intelligent par l'IA
        $aiAdvices = $aiCoachService->getMatchmakingAdvice($groups);

        // Obtenir l'évolution dans le temps pour le graphique HTML (pour l'entraîneur connecté)
        $evolution = $evaluationRepository->getTeamEvolutionOverTime($user);

        return $this->render('front_office/matchmaking/index.html.twig', [
            'groups' => $groups,
            'aiAdvices' => $aiAdvices,
            'evolution' => $evolution,
        ]);
    }
}
