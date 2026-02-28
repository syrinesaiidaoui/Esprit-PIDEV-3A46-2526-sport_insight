<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Entity\MatchLineup;
use App\Form\MatchsType;
use App\Repository\MatchsRepository;
use App\Repository\JoueurRepository;
use App\Service\MatchDetailAiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/matchs')]
final class FrontMatchsController extends AbstractController
{
    #[Route(name: 'app_front_matchs_index', methods: ['GET'])]
    public function index(MatchsRepository $matchsRepository, Request $request): Response
    {
        $sortOrder = $request->query->get('order', 'asc');
        $search = $request->query->get('search', '');
        $type = $request->query->get('type', '');
        $statut = $request->query->get('statut', '');
        $dateFrom = $request->query->get('dateFrom', '');
        $dateTo = $request->query->get('dateTo', '');

        $qb = $matchsRepository->createQueryBuilder('m');

        // Recherche textuelle : lieu, type, statut
        if ($search) {
            $qb->andWhere('LOWER(m.lieu) LIKE LOWER(:search) OR LOWER(m.type) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par type
        if ($type) {
            $qb->andWhere('m.type = :type')
                ->setParameter('type', $type);
        }

        // Filtre par statut
        if ($statut) {
            $qb->andWhere('m.statut = :statut')
                ->setParameter('statut', $statut);
        }

        // Filtre par date
        if ($dateFrom) {
            $qb->andWhere('m.dateMatch >= :dateFrom')
                ->setParameter('dateFrom', new \DateTime($dateFrom));
        }
        if ($dateTo) {
            $qb->andWhere('m.dateMatch <= :dateTo')
                ->setParameter('dateTo', new \DateTime($dateTo . ' 23:59:59'));
        }

        $matchs = $qb->orderBy('m.id', $sortOrder)
            ->getQuery()
            ->getResult();

        $deleteForms = [];
        foreach ($matchs as $match) {
            $deleteForms[$match->getId()] = $this->createFormBuilder()
                ->setAction($this->generateUrl('app_front_matchs_delete', ['id' => $match->getId()]))
                ->setMethod('POST')
                ->getForm()
                ->createView();
        }

        return $this->render('front_office/matchs/index.html.twig', [
            'matchs' => $matchs,
            'currentOrder' => $sortOrder,
            'delete_forms' => $deleteForms,
            'search' => $search,
            'type' => $type,
            'statut' => $statut,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    #[Route('/new', name: 'app_front_matchs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = new Matchs();
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($match);
            $entityManager->flush();
            return $this->redirectToRoute('app_front_matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front_office/matchs/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
            'joueurs_domicile_json' => '[]',
            'joueurs_exterieur_json' => '[]',
        ]);
    }

    #[Route('/{id}', name: 'app_front_matchs_show', methods: ['GET'])]
    public function show(Matchs $match): Response
    {
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_front_matchs_delete', ['id' => $match->getId()]))
            ->setMethod('POST')
            ->getForm();

        // pass OpenWeather API key to template (from env parameter)
        $weatherApiKey = $this->getParameter('app.openweather_api_key');

        // Logique dynamique pour le stade selon la ville du lieu du match
        $stadiums = [
            'paris' => 'Parc des Princes', 'marseille' => 'Orange Vélodrome', 'lyon' => 'Groupama Stadium', 'lille' => 'Stade Pierre Mauroy', 'monaco' => 'Stade Louis II', 'montpellier' => 'Stade de la Mosson', 'rennes' => 'Stade de la Route de Lorient', 'bordeaux' => 'Matmut Atlantique', 'toulouse' => 'Stadium de Toulouse', 'nice' => 'Allianz Riviera', 'strasbourg' => 'Stade de la Meinau', 'madrid' => 'Santiago Bernabéu', 'barcelona' => 'Spotify Camp Nou', 'barcel' => 'Spotify Camp Nou', 'sevilla' => 'Estadio Ramón Sánchez Pizjuán', 'bilbao' => 'San Mamés', 'valencia' => 'Estadio de Mestalla', 'atletico' => 'Wanda Metropolitano', 'seville' => 'Estadio Ramón Sánchez Pizjuán', 'rome' => 'Stadio Olimpico', 'roma' => 'Stadio Olimpico', 'milan' => 'San Siro', 'milano' => 'San Siro', 'napoli' => 'Stadio San Paolo', 'naples' => 'Stadio San Paolo', 'juventus' => 'Allianz Stadium', 'turin' => 'Allianz Stadium', 'torino' => 'Allianz Stadium', 'munich' => 'Allianz Arena', 'münchen' => 'Allianz Arena', 'dortmund' => 'Signal Iduna Park', 'berlin' => 'Olympiastadion', 'hamburg' => 'Volksparkstadion', 'cologne' => 'RheinEnergieStadion', 'köln' => 'RheinEnergieStadion', 'düsseldorf' => 'Merkur Spiel-Arena', 'leverkusen' => 'BayArena', 'schalke' => 'Veltins-Arena', 'london' => 'Wembley Stadium', 'londres' => 'Wembley Stadium', 'manchester' => 'Etihad Stadium', 'liverpool' => 'Anfield', 'arsenal' => 'Emirates Stadium', 'chelsea' => 'Stamford Bridge', 'tottenham' => 'Tottenham Hotspur Stadium', 'brighton' => 'Amex Stadium', 'leeds' => 'Elland Road', 'newcastle' => 'St James Park', 'everton' => 'Goodison Park', 'lisbon' => 'Estádio da Luz', 'lisbonne' => 'Estádio da Luz', 'lisboa' => 'Estádio da Luz', 'benfica' => 'Estádio da Luz', 'porto' => 'Estádio do Dragão', 'sporting' => 'Estádio José Alvalade', 'amsterdam' => 'Johan Cruyff Arena', 'rotterdam' => 'Feyenoord Stadium', 'ajax' => 'Johan Cruyff Arena', 'feyenoord' => 'Feyenoord Stadium', 'psv' => 'Philips Stadion', 'eindhoven' => 'Philips Stadion', 'brussels' => 'Stade Roi Baudouin', 'bruxelles' => 'Stade Roi Baudouin', 'brugge' => 'Jan Breydel Stadium', 'anderlecht' => 'Parc Astrid', 'tunis' => 'Stade Olympique de Radès', 'tunisia' => 'Stade Olympique de Radès', 'carthage' => 'Stade Olympique de Radès', 'athens' => 'Georgios Karaiskakis', 'athènes' => 'Georgios Karaiskakis', 'istanbul' => 'Başakşehir Park', 'ankara' => 'Eryaman Stadium', 'moscow' => 'Luzhniki Stadium', 'moscou' => 'Luzhniki Stadium', 'geneva' => 'Stade de Genève', 'genève' => 'Stade de Genève', 'zurich' => 'Stadion Letzigrund', 'vienna' => 'Red Bull Arena', 'vienne' => 'Red Bull Arena', 'prague' => 'Eden Arena', 'budapest' => 'Puskás Stadium', 'warsaw' => 'Stadion Narodowy', 'varsovie' => 'Stadion Narodowy',
        ];
        $lieu = strtolower($match->getLieu() ?? '');
        $stadiumGeo = ['nom' => 'Stade inconnu'];
        foreach ($stadiums as $ville => $nomStade) {
            if (strpos($lieu, $ville) !== false) {
                $stadiumGeo['nom'] = $nomStade;
                break;
            }
        }

        return $this->render('front_office/matchs/show.html.twig', [
            'match' => $match,
            'delete_form' => $deleteForm->createView(),
            'weatherApiKey' => $weatherApiKey,
            'stadiumGeo' => $stadiumGeo,
        ]);
    }

    #[Route('/{id}/ai-chat', name: 'app_front_matchs_ai_chat', methods: ['POST'])]
    public function aiChat(Request $request, Matchs $match, MatchDetailAiService $matchDetailAiService): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['reply' => 'Requete invalide.'], 400);
        }

        $message = trim((string)($payload['message'] ?? ''));
        if ($message === '') {
            return new JsonResponse(['reply' => 'Veuillez saisir un message.'], 400);
        }

        return new JsonResponse([
            'reply' => $matchDetailAiService->replyToMatchQuestion($match, $message),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_matchs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_front_matchs_delete', ['id' => $match->getId()]))
            ->setMethod('POST')
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_front_matchs_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->render('front_office/matchs/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'joueurs_domicile_json' => json_encode($joueursDomicileData),
            'joueurs_exterieur_json' => json_encode($joueursExterieurData),
        ]);
    }

    #[Route('/{id}/add-joueur', name: 'app_front_matchs_add_joueur', methods: ['POST'])]
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

    #[Route('/{id}/remove-joueur/{joueurId}', name: 'app_front_matchs_remove_joueur', methods: ['DELETE'])]
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

    #[Route('/{id}', name: 'app_front_matchs_delete', methods: ['POST'])]
    public function delete(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_' . $match->getId(), $request->request->get('_token'))) {
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_front_matchs_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/export-pdf', name: 'app_front_matchs_export_pdf', methods: ['GET'])]
    public function exportPdf(Matchs $match, \Knp\Snappy\Pdf $knpSnappy): Response
    {
        // Logique pour le stade (même que dans show())
        $stadiums = [
            'paris' => 'Parc des Princes', 'marseille' => 'Orange Vélodrome', 'lyon' => 'Groupama Stadium', 'lille' => 'Stade Pierre Mauroy', 'monaco' => 'Stade Louis II', 'montpellier' => 'Stade de la Mosson', 'rennes' => 'Stade de la Route de Lorient', 'bordeaux' => 'Matmut Atlantique', 'toulouse' => 'Stadium de Toulouse', 'nice' => 'Allianz Riviera', 'strasbourg' => 'Stade de la Meinau', 'madrid' => 'Santiago Bernabéu', 'barcelona' => 'Spotify Camp Nou', 'barcel' => 'Spotify Camp Nou', 'sevilla' => 'Estadio Ramón Sánchez Pizjuán', 'bilbao' => 'San Mamés', 'valencia' => 'Estadio de Mestalla', 'atletico' => 'Wanda Metropolitano', 'seville' => 'Estadio Ramón Sánchez Pizjuán', 'rome' => 'Stadio Olimpico', 'roma' => 'Stadio Olimpico', 'milan' => 'San Siro', 'milano' => 'San Siro', 'napoli' => 'Stadio San Paolo', 'naples' => 'Stadio San Paolo', 'juventus' => 'Allianz Stadium', 'turin' => 'Allianz Stadium', 'torino' => 'Allianz Stadium', 'munich' => 'Allianz Arena', 'münchen' => 'Allianz Arena', 'dortmund' => 'Signal Iduna Park', 'berlin' => 'Olympiastadion', 'hamburg' => 'Volksparkstadion', 'cologne' => 'RheinEnergieStadion', 'köln' => 'RheinEnergieStadion', 'düsseldorf' => 'Merkur Spiel-Arena', 'leverkusen' => 'BayArena', 'schalke' => 'Veltins-Arena', 'london' => 'Wembley Stadium', 'londres' => 'Wembley Stadium', 'manchester' => 'Etihad Stadium', 'liverpool' => 'Anfield', 'arsenal' => 'Emirates Stadium', 'chelsea' => 'Stamford Bridge', 'tottenham' => 'Tottenham Hotspur Stadium', 'brighton' => 'Amex Stadium', 'leeds' => 'Elland Road', 'newcastle' => 'St James Park', 'everton' => 'Goodison Park', 'lisbon' => 'Estádio da Luz', 'lisbonne' => 'Estádio da Luz', 'lisboa' => 'Estádio da Luz', 'benfica' => 'Estádio da Luz', 'porto' => 'Estádio do Dragão', 'sporting' => 'Estádio José Alvalade', 'amsterdam' => 'Johan Cruyff Arena', 'rotterdam' => 'Feyenoord Stadium', 'ajax' => 'Johan Cruyff Arena', 'feyenoord' => 'Feyenoord Stadium', 'psv' => 'Philips Stadion', 'eindhoven' => 'Philips Stadion', 'brussels' => 'Stade Roi Baudouin', 'bruxelles' => 'Stade Roi Baudouin', 'brugge' => 'Jan Breydel Stadium', 'anderlecht' => 'Parc Astrid', 'tunis' => 'Stade Olympique de Radès', 'tunisia' => 'Stade Olympique de Radès', 'carthage' => 'Stade Olympique de Radès', 'athens' => 'Georgios Karaiskakis', 'athènes' => 'Georgios Karaiskakis', 'istanbul' => 'Başakşehir Park', 'ankara' => 'Eryaman Stadium', 'moscow' => 'Luzhniki Stadium', 'moscou' => 'Luzhniki Stadium', 'geneva' => 'Stade de Genève', 'genève' => 'Stade de Genève', 'zurich' => 'Stadion Letzigrund', 'vienna' => 'Red Bull Arena', 'vienne' => 'Red Bull Arena', 'prague' => 'Eden Arena', 'budapest' => 'Puskás Stadium', 'warsaw' => 'Stadion Narodowy', 'varsovie' => 'Stadion Narodowy',
        ];
        $lieu = strtolower($match->getLieu() ?? '');
        $stadiumGeo = ['nom' => 'Stade inconnu'];
        foreach ($stadiums as $ville => $nomStade) {
            if (strpos($lieu, $ville) !== false) {
                $stadiumGeo['nom'] = $nomStade;
                break;
            }
        }

        // Générer le HTML du PDF
        $html = $this->renderView('front_office/matchs/export_pdf.html.twig', [
            'match' => $match,
            'stadiumGeo' => $stadiumGeo,
        ]);

        // Convertir les URLs relatives en chemins absolus pour wkhtmltopdf
        $publicDir = $this->getParameter('kernel.project_dir') . '/public';
        $html = preg_replace_callback(
            '/src=["\']\/([^"\']+)["\']/',
            function($matches) use ($publicDir) {
                $filePath = $publicDir . '/' . $matches[1];
                if (file_exists($filePath)) {
                    // Convertir le chemin Windows en URL file://
                    $fileUrl = 'file:///' . str_replace('\\', '/', realpath($filePath));
                    return 'src="' . $fileUrl . '"';
                }
                return $matches[0];
            },
            $html
        );

        // Générer le PDF en sauvegardant d'abord le HTML dans un fichier temporaire
        $tempHtmlFile = tempnam(sys_get_temp_dir(), 'match_') . '.html';
        file_put_contents($tempHtmlFile, $html);

        $tempFile = tempnam(sys_get_temp_dir(), 'match_') . '.pdf';

        try {
            // Convertir le chemin Windows en URL file:// (3 slashes pour Windows)
            $fileUrl = 'file:///' . str_replace('\\', '/', $tempHtmlFile);
            
            $knpSnappy->generate($fileUrl, $tempFile, [
                'page-size' => 'A4',
                'margin-top' => 10,
                'margin-right' => 10,
                'margin-bottom' => 10,
                'margin-left' => 10,
                'dpi' => 300,
                'lowquality' => false,
                'enable-local-file-access' => true,
                'allow' => sys_get_temp_dir(),
                'load-error-handling' => 'ignore',
                'load-media-error-handling' => 'ignore',
            ]);

            $pdf = file_get_contents($tempFile);
            
            // Nettoyage des fichiers temporaires
            @unlink($tempHtmlFile);
            @unlink($tempFile);

            return new Response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="match_' . $match->getId() . '.pdf"',
            ]);
        } catch (\Exception $e) {
            // Nettoyage en cas d'erreur
            @unlink($tempHtmlFile);
            @unlink($tempFile);
            throw $e;
        }
    }
}
