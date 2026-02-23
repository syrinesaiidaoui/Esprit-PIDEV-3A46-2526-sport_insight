<?php

namespace App\Controller\FrontOffice;

use App\Service\AiCoachService;
use App\Service\NutritionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AiCoachController extends AbstractController
{
    #[Route('/front/coach/api/advice', name: 'front_ai_coach_api')]
    public function apiAdvice(Request $request, AiCoachService $aiService): JsonResponse
    {
        $p = $request->query->get('p');
        $t = $request->query->get('t');
        $ta = $request->query->get('ta');
        $type = $request->query->get('type');

        if (!$p || !$t || !$ta) {
            return new JsonResponse(['advice' => "Données manquantes pour l'analyse."], 400);
        }

        try {
            $structured = $aiService->generateStructuredAdvice($p, $t, $ta, $type);
            
            return new JsonResponse($structured);
        } catch (\Exception $e) {
            return new JsonResponse([
                'advice' => "Erreur lors de la génération des conseils : " . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/front/coach/api/nutrition', name: 'front_ai_nutrition_api')]
    public function nutritionAdvice(Request $request, NutritionService $nutritionService): JsonResponse
    {
        $p = (float)$request->query->get('p', 10);
        $t = (float)$request->query->get('t', 10);
        $ta = (float)$request->query->get('ta', 10);
        $type = $request->query->get('type', '');

        try {
            $nutrition = $nutritionService->generateNutritionAdvice($p, $t, $ta, $type);
            
            return new JsonResponse($nutrition);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => "Erreur lors de la génération des conseils nutrition : " . $e->getMessage()
            ], 500);
        }
    }
}