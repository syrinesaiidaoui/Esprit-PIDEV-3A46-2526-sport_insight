<?php

namespace App\EventSubscriber;

use App\Repository\ContratSponsorRepository;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ContractExpirationSubscriber implements EventSubscriberInterface
{
    private ContratSponsorRepository $contratRepo;
    private TwilioService $twilio;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        ContratSponsorRepository $contratRepo,
        TwilioService $twilio,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->contratRepo = $contratRepo;
        $this->twilio = $twilio;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Ne pas exécuter sur les sous-requêtes
        if (!$event->isMainRequest()) {
            return;
        }

        $qb = $this->contratRepo->createQueryBuilder('c')
            ->where('c.dateFin < :today')
            ->andWhere('c.notified = false')
            ->setParameter('today', new \DateTime());
            
        $expiredContracts = $qb->getQuery()->getResult();

        $sentCount = 0;

        foreach ($expiredContracts as $contrat) {
            // On s'assure qu'on n'envoie pas le SMS deux fois
            if (!$contrat->isNotified()) {
                $equipePhone = $contrat->getEquipe()?->getTelephone();
                $sponsorName = $contrat->getSponsor()?->getNom() ?? 'Unknown Sponsor';

                if ($equipePhone) {
                    $smsMessage = sprintf(
                        "Alerte: Le contrat avec le sponsor %s est arrivé à expiration.",
                        $sponsorName
                    );

                    // Envoyer le SMS
                    $success = $this->twilio->sendSms($equipePhone, $smsMessage);

                    if ($success) {
                        $this->logger->info("SMS automatisé envoyé pour le contrat expiré #{$contrat->getId()}");
                        $contrat->setNotified(true);
                        $this->entityManager->persist($contrat);
                        $sentCount++;
                    } else {
                        $this->logger->error("Échec de l'envoi du SMS automatisé pour le contrat #{$contrat->getId()}");
                    }
                }
            }
        }

        // Sauvegarder les changements
        if ($sentCount > 0) {
            $this->entityManager->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
