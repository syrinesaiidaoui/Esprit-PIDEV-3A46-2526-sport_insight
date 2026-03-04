<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/joueurs')]
class FrontJoueurController extends AbstractController
{
    private const MIN_SEARCH_LENGTH = 2;
    private const AJAX_RESULTS_LIMIT = 140;

    #[Route('', name: 'app_joueurs_index', methods: ['GET'])]
    public function index(JoueurRepository $joueurRepository, EquipeRepository $equipeRepository, Request $request): Response
    {
        [$q, $equipeId] = $this->extractFilters($request);
        $joueurs = $this->findFilteredJoueurs($joueurRepository, $q, $equipeId);
        $joueursGroupes = $this->groupJoueursByEquipe($joueurs);
        $equipes = $equipeRepository->findBy([], ['nom' => 'ASC']);

        return $this->render('joueur/index.html.twig', [
            'joueursGroupes' => $joueursGroupes,
            'equipes' => $equipes,
            'q' => $q,
            'selectedEquipe' => $equipeId,
            'totalPlayers' => count($joueurs),
            'totalTeams' => count($joueursGroupes),
        ]);
    }

    #[Route('/search', name: 'app_joueurs_search', methods: ['GET'])]
    public function search(Request $request, JoueurRepository $joueurRepository): JsonResponse
    {
        [$q, $equipeId] = $this->extractFilters($request);
        $joueurs = $this->findFilteredJoueurs($joueurRepository, $q, $equipeId, self::AJAX_RESULTS_LIMIT);
        $joueursGroupes = $this->groupJoueursByEquipe($joueurs);

        $html = $this->renderView('joueur/_groups.html.twig', [
            'joueursGroupes' => $joueursGroupes,
        ]);

        return new JsonResponse([
            'html' => $html,
            'count' => count($joueurs),
            'groupCount' => count($joueursGroupes),
        ]);
    }

    #[Route('/{id}', name: 'app_joueur_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
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

    #[Route('/equipe/{equipeId}', name: 'app_joueurs_by_equipe', methods: ['GET'], requirements: ['equipeId' => '\\d+'])]
    public function byEquipe(int $equipeId, JoueurRepository $joueurRepository, EquipeRepository $equipeRepository): Response
    {
        $equipe = $equipeRepository->find($equipeId);

        if (!$equipe) {
            throw $this->createNotFoundException('L\'equipe n\'existe pas');
        }

        $joueurs = $joueurRepository->findByEquipe($equipeId);

        return $this->render('joueur/by_equipe.html.twig', [
            'equipe' => $equipe,
            'joueurs' => $joueurs,
            'now' => new \DateTime(),
        ]);
    }

    /**
     * @return array{string, ?int}
     */
    private function extractFilters(Request $request): array
    {
        $q = trim((string) $request->query->get('q', $request->query->get('search', '')));
        $equipeRaw = trim((string) $request->query->get('equipe', ''));

        $equipeId = null;
        if ($equipeRaw !== '' && ctype_digit($equipeRaw)) {
            $parsed = (int) $equipeRaw;
            if ($parsed > 0) {
                $equipeId = $parsed;
            }
        }

        return [$q, $equipeId];
    }

    /**
     * @return Joueur[]
     */
    private function findFilteredJoueurs(
        JoueurRepository $joueurRepository,
        string $q,
        ?int $equipeId,
        ?int $limit = null
    ): array {
        $normalizedSearch = $this->normalizeSearchTerm($q);

        $qb = $joueurRepository->createQueryBuilder('j')
            ->leftJoin('j.equipe', 'e')
            ->addSelect('e');

        if ($normalizedSearch !== '') {
            $searchPattern = '%' . mb_strtolower($normalizedSearch) . '%';
            $qb->andWhere('(
                    LOWER(j.nom) LIKE :search
                    OR LOWER(j.prenom) LIKE :search
                    OR LOWER(CONCAT(j.prenom, \' \, j.nom)) LIKE :search
                    OR LOWER(CONCAT(j.nom, \' \, j.prenom)) LIKE :search
                    OR LOWER(e.nom) LIKE :search
                )')
                ->setParameter('search', $searchPattern);
        }

        if ($equipeId !== null) {
            $qb->andWhere('e.id = :equipeId')
                ->setParameter('equipeId', $equipeId);
        }

        $qb->orderBy('e.nom', 'ASC')
            ->addOrderBy('j.numero', 'ASC')
            ->addOrderBy('j.nom', 'ASC')
            ->addOrderBy('j.prenom', 'ASC');

        if (is_int($limit) && $limit > 0) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    private function normalizeSearchTerm(string $term): string
    {
        $value = trim($term);
        if ($value === '' || mb_strlen($value) < self::MIN_SEARCH_LENGTH) {
            return '';
        }

        return $value;
    }

    /**
     * @param Joueur[] $joueurs
     *
     * @return array<int, array{equipe: \App\Entity\Equipe, joueurs: Joueur[]}>
     */
    private function groupJoueursByEquipe(array $joueurs): array
    {
        $grouped = [];

        foreach ($joueurs as $joueur) {
            $equipe = $joueur->getEquipe();
            if ($equipe === null || $equipe->getId() === null) {
                continue;
            }

            $equipeId = $equipe->getId();
            if (!isset($grouped[$equipeId])) {
                $grouped[$equipeId] = [
                    'equipe' => $equipe,
                    'joueurs' => [],
                ];
            }

            $grouped[$equipeId]['joueurs'][] = $joueur;
        }

        return $grouped;
    }
}
