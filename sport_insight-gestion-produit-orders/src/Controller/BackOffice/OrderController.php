<?php

namespace App\Controller\BackOffice;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'back_orders_index')]
    public function index(OrderRepository $repo): Response
    {
<<<<<<< HEAD
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
=======
        // removed auth check for public access
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
        $orders = $repo->findAll();
        return $this->render('back_office/order/index.html.twig', ['orders' => $orders]);
    }
}
