<?php

namespace App\Controller\BackOffice;

use App\Repository\SponsorRepository;
use App\Repository\ContratSponsorRepository;
<<<<<<< HEAD
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
=======
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
>>>>>>> origin/rym-sponsoring

#[Route('/admin/sponsoring')]
class SponsoringController extends AbstractController
{
    #[Route('/', name: 'back_sponsoring_index')]
<<<<<<< HEAD
    public function index(
        Request $request, 
        SponsorRepository $sponsorRepository, 
        ContratSponsorRepository $contratSponsorRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UserRepository $userRepository,
        LoggerInterface $logger
    ): Response
=======
    public function index(Request $request, SponsorRepository $sponsorRepository, ContratSponsorRepository $contratSponsorRepository): Response
>>>>>>> origin/rym-sponsoring
    {
        $sponsors = $sponsorRepository->findAll();
        
        $sponsorNom = $request->query->get('sponsor_nom');
        $dateDebut = $request->query->get('date_debut');

        // Convertir la date si elle est fournie
        $dateDebutObj = null;
        if ($dateDebut) {
            try {
                $dateDebutObj = new \DateTime($dateDebut);
            } catch (\Exception $e) {
                $dateDebutObj = null;
            }
        }

        // Rechercher avec les critères
        if ($sponsorNom || $dateDebutObj) {
            $contrats = $contratSponsorRepository->searchContrats($sponsorNom, $dateDebutObj);
        } else {
            $contrats = $contratSponsorRepository->findAll();
        }
<<<<<<< HEAD

        // Mettre à jour le statut des contrats expirés et envoyer les emails
        $now = new \DateTime();
        $expiredCount = 0;
        
        foreach ($contrats as $contrat) {
            $isExpired = $contrat->isExpired();
            $currentStatut = $contrat->getStatut();
            
            if ($isExpired && $currentStatut !== 'Expiré') {
                $contrat->setStatut('Expiré');
                $entityManager->persist($contrat);
                $expiredCount++;
                
                $logger->info("Contrat expiré détecté: " . $contrat->getId());

                // Envoyer un email de notification
                $admins = $userRepository->findAdmins();
                $sponsorNom = $contrat->getSponsor()->getNom();
                $equipeNom = $contrat->getEquipe()->getNom();
                $dateFin = $contrat->getDateFin()->format('d/m/Y');
                
                $logger->info("Nombre d'admins trouvés: " . count($admins));
                
                if (count($admins) > 0) {
                    $email = (new Email())
                        ->from('noreply@sportclub.com')
                        ->subject('⚠️ Contrat sponsor expiré')
                        ->text("Le contrat du sponsor {$sponsorNom} avec l'équipe {$equipeNom} a expiré le {$dateFin}.");
                    
                    foreach ($admins as $admin) {
                        $email->addTo($admin->getEmail());
                        $logger->info("Email envoyé à: " . $admin->getEmail());
                    }
                    
                    try {
                        $mailer->send($email);
                        $logger->info("Email envoyé avec succès");
                    } catch (\Exception $e) {
                        $logger->error("Erreur envoi email: " . $e->getMessage());
                    }
                } else {
                    $logger->warning("Aucun administrateur trouvé pour l'email");
                }
            }
        }
        
        if ($expiredCount > 0) {
            $entityManager->flush();
        }

        // Calculer les statistiques
        $totalSponsors = count($sponsors);
        $totalContrats = count($contrats);
        
        $budgetTotal = array_reduce($sponsors, fn($carry, $sponsor) => $carry + $sponsor->getBudget(), 0);
        $montantTotal = array_reduce($contrats, fn($carry, $contrat) => $carry + $contrat->getMontant(), 0);
        $valeurMoyenne = count($contrats) > 0 ? $montantTotal / count($contrats) : 0;
=======
>>>>>>> origin/rym-sponsoring
        
        return $this->render('back_office/sponsoring/index.html.twig', [
            'sponsors' => $sponsors,
            'contrats' => $contrats,
            'sponsor_nom' => $sponsorNom,
            'date_debut' => $dateDebut,
<<<<<<< HEAD
            'totalSponsors' => $totalSponsors,
            'totalContrats' => $totalContrats,
            'budgetTotal' => $budgetTotal,
            'montantTotal' => $montantTotal,
            'valeurMoyenne' => $valeurMoyenne,
=======
>>>>>>> origin/rym-sponsoring
        ]);
    }
}
