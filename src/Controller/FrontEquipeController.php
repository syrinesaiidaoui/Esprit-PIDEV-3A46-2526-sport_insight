<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/equipes')]
final class FrontEquipeController extends AbstractController
{
    private const MIN_TEAM_SEARCH_LENGTH = 2;
    private const AJAX_RESULTS_LIMIT = 60;

    #[Route(name: 'app_front_equipes_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, Request $request): Response
    {
        $team = trim((string) $request->query->get('team', $request->query->get('search', '')));
        $equipes = $this->findFilteredEquipes($equipeRepository, $team);

        return $this->render('front_office/equipes/index.html.twig', [
            'equipes' => $equipes,
            'team' => $team,
        ]);
    }

    #[Route('/search', name: 'app_front_equipes_search', methods: ['GET'])]
    public function search(Request $request, EquipeRepository $equipeRepository): JsonResponse
    {
        $team = trim((string) $request->query->get('team', $request->query->get('search', '')));
        $equipes = $this->findFilteredEquipes($equipeRepository, $team, self::AJAX_RESULTS_LIMIT);

        $html = $this->renderView('front_office/equipes/_cards.html.twig', [
            'equipes' => $equipes,
        ]);

        return new JsonResponse([
            'html' => $html,
            'count' => count($equipes),
        ]);
    }

    #[Route('/{id}', name: 'app_front_equipes_show', methods: ['GET'], requirements: ['id' => '\d+'])]
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

    private function findFilteredEquipes(EquipeRepository $equipeRepository, string $team, ?int $limit = null): array
    {
        $normalizedTeam = $this->normalizeTeamTerm($team);
        $qb = $equipeRepository->createQueryBuilder('e');

        if ($normalizedTeam !== '') {
            $qb->andWhere('e.nom LIKE :teamPrefix')
                ->setParameter('teamPrefix', $normalizedTeam . '%');
        }

        $qb->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.id', 'ASC');

        if (is_int($limit) && $limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    private function normalizeTeamTerm(string $team): string
    {
        $value = trim($team);
        if ($value === '' || mb_strlen($value) < self::MIN_TEAM_SEARCH_LENGTH) {
            return '';
        }

        return $value;
    }
}
