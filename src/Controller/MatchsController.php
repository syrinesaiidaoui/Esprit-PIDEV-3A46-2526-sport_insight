<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Entity\MatchLineup;
use App\Form\MatchsType;
use App\Repository\MatchsRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/matchs')]
final class MatchsController extends AbstractController
{
    #[Route('/{id}/add-joueur', name: 'app_matchs_add_joueur', methods: ['POST'])]
    public function addJoueur(Request $request, Matchs $match, JoueurRepository $joueurRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $joueurId = $data['joueur_id'] ?? null;
        $type = $data['type'] ?? null;

        if (!$joueurId || !$type) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $joueur = $joueurRepository->find($joueurId);
        if (!$joueur) {
            return $this->json(['error' => 'Joueur not found'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le joueur est déjà ajouté
        $existing = $match->getMatchLineups()->filter(fn($l) => 
            $l->getJoueur()->getId() === $joueurId && $l->getType() === $type
        );

        if ($existing->count() > 0) {
            return $this->json(['error' => 'Joueur already added'], Response::HTTP_CONFLICT);
        }

        // Créer et persister la composition
        $matchLineup = new MatchLineup();
        $matchLineup->setMatchs($match);
        $matchLineup->setJoueur($joueur);
        $matchLineup->setType($type);
        $entityManager->persist($matchLineup);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'id' => $matchLineup->getId(),
            'joueur_id' => $joueur->getId(),
            'nom' => $joueur->getNom(),
            'prenom' => $joueur->getPrenom(),
            'numero' => $joueur->getNumero()
        ]);
    }

    #[Route('/{id}/remove-joueur/{joueurId}', name: 'app_matchs_remove_joueur', methods: ['DELETE'])]
    public function removeJoueur(Request $request, Matchs $match, int $joueurId, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $type = $data['type'] ?? null;

        if (!$type) {
            return $this->json(['error' => 'Invalid type'], Response::HTTP_BAD_REQUEST);
        }

        $lineup = $match->getMatchLineups()->filter(fn($l) => 
            $l->getJoueur()->getId() === $joueurId && $l->getType() === $type
        )->first();

        if (!$lineup) {
            return $this->json(['error' => 'Lineup not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($lineup);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route(name: 'app_matchs_index', methods: ['GET'])]
    public function index(MatchsRepository $matchsRepository, Request $request): Response
    {
        $sortOrder = $request->query->get('order', 'asc'); // Default order is 'asc'
        $search = $request->query->get('search', '');

        $qb = $matchsRepository->createQueryBuilder('m');

        // Recherche textuelle : lieu
        if ($search) {
            $qb->andWhere('LOWER(m.lieu) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        $matchs = $qb->orderBy('m.id', $sortOrder)
            ->getQuery()
            ->getResult();

        return $this->render('matchs/index.html.twig', [
            'matchs' => $matchs,
            'currentOrder' => $sortOrder,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_matchs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = new Matchs();
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($match);
            $entityManager->flush();
            return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matchs/new.html.twig', [
            'match' => $match,
            'form' => $form,
            'joueurs_domicile_json' => '[]',
            'joueurs_exterieur_json' => '[]',
        ]);
    }

    #[Route('/{id}', name: 'app_matchs_show', methods: ['GET'])]
    public function show(Matchs $match): Response
    {
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_matchs_delete', ['id' => $match->getId()]))
            ->setMethod('POST')
            ->getForm();

        // Récupérer les lineups triés par numéro
        $domicileLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'domicile')->toArray();
        $exterieurLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'exterieur')->toArray();

        // Trier par numéro
        usort($domicileLineups, fn($a, $b) => $a->getJoueur()->getNumero() <=> $b->getJoueur()->getNumero());
        usort($exterieurLineups, fn($a, $b) => $a->getJoueur()->getNumero() <=> $b->getJoueur()->getNumero());

        return $this->render('matchs/show.html.twig', [
            'match' => $match,
            'delete_form' => $deleteForm->createView(),
            'domicileLineups' => $domicileLineups,
            'exterieurLineups' => $exterieurLineups,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_matchs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_matchs_delete', ['id' => $match->getId()]))
            ->setMethod('POST')
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        $domicileLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'domicile')->toArray();
        $exterieurLineups = $match->getMatchLineups()->filter(fn($l) => $l->getType() === 'exterieur')->toArray();

        $formatJoueur = function($lineup) {
            return [
                'id' => $lineup->getJoueur()->getId(),
                'nom' => $lineup->getJoueur()->getNom(),
                'prenom' => $lineup->getJoueur()->getPrenom(),
                'numero' => $lineup->getJoueur()->getNumero(),
            ];
        };

        $joueursDomicileData = array_map($formatJoueur, $domicileLineups);
        $joueursExterieurData = array_map($formatJoueur, $exterieurLineups);

        return $this->render('matchs/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'joueurs_domicile_json' => json_encode($joueursDomicileData),
            'joueurs_exterieur_json' => json_encode($joueursExterieurData),
        ]);
    }

    #[Route('/{id}', name: 'app_matchs_delete', methods: ['POST'])]
    public function delete(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($match);
        $entityManager->flush();

        return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
    }
}

