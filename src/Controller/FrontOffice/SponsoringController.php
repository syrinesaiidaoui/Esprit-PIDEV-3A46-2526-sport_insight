<?php

namespace App\Controller\FrontOffice;

use App\Repository\ContratSponsorRepository;
use App\Repository\SponsorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/sponsoring')]
class SponsoringController extends AbstractController
{
    #[Route('/', name: 'front_sponsoring_index')]
    public function index(
        Request $request,
        SponsorRepository $sponsorRepository,
        ContratSponsorRepository $contratSponsorRepository,
        \Knp\Component\Pager\PaginatorInterface $paginator,
        HttpClientInterface $httpClient,
        CacheInterface $cache
    ): Response {
        $sponsors = $sponsorRepository->findAll();
        $sponsorCoordinates = [];

        foreach ($sponsors as $sponsor) {
            $adresse = trim((string) $sponsor->getAdresse());
            if ($adresse === '') {
                continue;
            }

            $coords = $this->geocodeAddress($adresse, $httpClient, $cache);
            if ($coords !== null && $sponsor->getId() !== null) {
                $sponsorCoordinates[$sponsor->getId()] = $coords;
            }
        }

        $sponsorNom = $request->query->get('sponsor_nom');
        $dateDebut = $request->query->get('date_debut');

        $dateDebutObj = null;
        if ($dateDebut) {
            try {
                $dateDebutObj = new \DateTime($dateDebut);
            } catch (\Exception $e) {
                $dateDebutObj = null;
            }
        }

        if ($sponsorNom || $dateDebutObj) {
            $query = $contratSponsorRepository->searchContrats($sponsorNom, $dateDebutObj);
        } else {
            $qb = $contratSponsorRepository->createQueryBuilder('c')
                ->addSelect('s')
                ->addSelect('e')
                ->innerJoin('c.sponsor', 's')
                ->innerJoin('c.equipe', 'e')
                ->orderBy('c.dateDebut', 'DESC');
            $query = $qb->getQuery();
        }

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2 // items per page
        );

        return $this->render('front_office/sponsoring/index.html.twig', [
            'sponsors' => $sponsors,
            'sponsorCoordinates' => $sponsorCoordinates,
            'contrats' => $pagination,
            'sponsor_nom' => $sponsorNom,
            'date_debut' => $dateDebut,
        ]);
    }

    private function geocodeAddress(string $address, HttpClientInterface $httpClient, CacheInterface $cache): ?array
    {
        $cacheKey = 'sponsor_geo_' . sha1(mb_strtolower(trim($address)));

        return $cache->get($cacheKey, function (ItemInterface $item) use ($address, $httpClient): ?array {
            $item->expiresAfter(60 * 60 * 24 * 30);

            try {
                $response = $httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                    'query' => [
                        'format' => 'jsonv2',
                        'limit' => 1,
                        'countrycodes' => 'tn',
                        'q' => $address,
                    ],
                    'headers' => [
                        'User-Agent' => 'SportInsight/1.0 (local geocoding)',
                        'Accept-Language' => 'fr',
                    ],
                    'timeout' => 8,
                ]);

                $results = $response->toArray(false);

                // Respect Nominatim's 1 request/second policy for uncached lookups.
                usleep(1100000);

                if (!isset($results[0]['lat'], $results[0]['lon'])) {
                    // Retry unknown addresses earlier in case data improves.
                    $item->expiresAfter(60 * 60 * 6);
                    return null;
                }

                return [
                    'lat' => (float) $results[0]['lat'],
                    'lon' => (float) $results[0]['lon'],
                ];
            } catch (\Throwable) {
                // Network or provider issue: retry soon.
                $item->expiresAfter(60 * 10);
                return null;
            }
        });
    }
}
