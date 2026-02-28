<?php

namespace App\Controller\BackOffice;

use App\Entity\ContratSponsor;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/payment')]
class PaymentController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'app_payment_checkout')]
    public function checkout(ContratSponsor $contrat): Response
    {
        // On vérifie que le contrat n'est pas déjà payé
        if ($contrat->getStatutPaiement() === 'Payé') {
            $this->addFlash('warning', 'Ce contrat est déjà payé.');
            return $this->redirectToRoute('back_sponsoring_index');
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur', // Devise changée de TND à EUR car TND n'est pas supporté par défaut
                    'unit_amount' => $contrat->getMontant() * 100, // Stripe attend le montant en centimes (ex: 4500 EUR -> 450000 centimes)
                    'product_data' => [
                        'name' => 'Contrat Sponsoring - ' . $contrat->getSponsor()->getNom(),
                        'description' => 'Paiement du contrat de sponsoring pour la période du ' . $contrat->getDateDebut()->format('d/m/Y') . ' au ' . $contrat->getDateFin()->format('d/m/Y'),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', ['id' => $contrat->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_payment_cancel', ['id' => $contrat->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkout_session->url, 303);
    }

    #[Route('/success/{id}', name: 'app_payment_success')]
    public function success(ContratSponsor $contrat, EntityManagerInterface $em): Response
    {
        // Mettre à jour le statut du contrat
        $contrat->setStatutPaiement('Payé');
        $em->flush();

        $this->addFlash('success', 'Paiement Stripe réussi ! Le contrat est maintenant marqué comme payé.');
        return $this->redirectToRoute('back_sponsoring_index');
    }

    #[Route('/cancel/{id}', name: 'app_payment_cancel')]
    public function cancel(ContratSponsor $contrat): Response
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('back_sponsoring_index');
    }
}
