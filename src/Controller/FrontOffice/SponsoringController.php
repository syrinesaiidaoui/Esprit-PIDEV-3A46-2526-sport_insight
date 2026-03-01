<?php

namespace App\Controller\FrontOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
=======
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SponsorRepository;
use App\Repository\ContratSponsorRepository;
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d

#[Route('/sponsoring')]
class SponsoringController extends AbstractController
{
    #[Route('/', name: 'front_sponsoring_index')]
<<<<<<< HEAD
    public function index(): Response
    {
        return $this->render('front_office/sponsoring/index.html.twig');
=======
    public function index(Request $request, SponsorRepository $sponsorRepository, ContratSponsorRepository $contratSponsorRepository, \Knp\Component\Pager\PaginatorInterface $paginator): Response
    {
        $sponsors = $sponsorRepository->findAll();

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
            'contrats' => $pagination,
            'sponsor_nom' => $sponsorNom,
            'date_debut' => $dateDebut,
        ]);
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    }
}
