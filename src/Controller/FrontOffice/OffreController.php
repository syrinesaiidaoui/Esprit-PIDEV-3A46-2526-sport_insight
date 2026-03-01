<?php

namespace App\Controller\FrontOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<<<<<<< HEAD
#[Route('/offre')]
class OffreController extends AbstractController
{
    #[Route('/', name: 'front_offre_index')]
=======
// Feature removed: disable route
//#[Route('/offre')]
class OffreController extends AbstractController
{
    //#[Route('/', name: 'front_offre_index')]
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    public function index(): Response
    {
        return $this->render('front_office/offre/index.html.twig');
    }
}
