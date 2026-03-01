<?php

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<<<<<<< HEAD
#[Route('/admin/offre')]
class OffreController extends AbstractController
{
    #[Route('/', name: 'back_offre_index')]
=======
// Feature removed: disable route
//#[Route('/admin/offre')]
class OffreController extends AbstractController
{
    //#[Route('/', name: 'back_offre_index')]
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    public function index(): Response
    {
        return $this->render('back_office/offre/index.html.twig');
    }
}
