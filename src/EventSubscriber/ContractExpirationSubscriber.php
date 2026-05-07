<?php

namespace App\EventSubscriber;

use App\Repository\ContratSponsorRepository;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ContractExpirationSubscriber implements EventSubscriberInterface
{
    private const CHECK_INTERVAL_SECONDS = 300;

    private bool $shouldRunCheck = false;

    public function __construct(
        private ContratSponsorRepository $contratRepo,
        private TwilioService $twilio,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private CacheInterface $cache
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = (string) $event->getRequest()->attributes->get('_route', '');
        if ($route === 'web_profiler_wdt' || $route === 'web_profiler_profiler') {
            return;
        }

        $shouldRunCheck = false;

        $this->cache->get('contracts.expiration.periodic_check.v1', function (ItemInterface $item) use (&$shouldRunCheck): bool {
            $item->expiresAfter(self::CHECK_INTERVAL_SECONDS);
            $shouldRunCheck = true;

            return true;
        });

        $this->shouldRunCheck = $shouldRunCheck;
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$this->shouldRunCheck) {
            return;
        }

        $this->shouldRunCheck = false;
        $this->runExpirationCheck();
    }

    private function runExpirationCheck(): void
    {
        $this->logger->info('ContractExpirationSubscriber: Checking for expired contracts...');

        $qb = $this->contratRepo->createQueryBuilder('c')
            ->where('c.dateFin < :today')
            ->andWhere('c.notified = false')
            ->setParameter('today', new \DateTime());

        $expiredContracts = $qb->getQuery()->getResult();
        $sentCount = 0;

        foreach ($expiredContracts as $contrat) {
            if ($contrat->isNotified()) {
                continue;
            }

            $equipePhone = $contrat->getEquipe()?->getTelephone();
            $sponsorName = $contrat->getSponsor()?->getNom() ?? 'Unknown Sponsor';

            if (!$equipePhone) {
                continue;
            }

            $smsMessage = sprintf(
                'Alerte: Le contrat avec le sponsor %s est arrive a expiration.',
                $sponsorName
            );

            $success = $this->twilio->sendSms($equipePhone, $smsMessage);

            if ($success) {
                $this->logger->info(sprintf('Automated SMS sent for expired contract #%d', $contrat->getId()));
                $contrat->setNotified(true);
                $this->entityManager->persist($contrat);
                $sentCount++;
            } else {
                $this->logger->error(sprintf('Automated SMS failed for contract #%d', $contrat->getId()));
            }
        }

        if ($sentCount > 0) {
            $this->entityManager->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
