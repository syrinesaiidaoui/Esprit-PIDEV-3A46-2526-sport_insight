<?php

namespace App\Command;

use App\Repository\ContratSponsorRepository;
use App\Service\TwilioService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:contract:expiration',
    description: 'Check for expired contracts and send SMS alerts to team contacts'
)]
class ContractExpirationCommand extends Command
{
    private ContratSponsorRepository $contratRepo;
    private TwilioService $twilio;

    public function __construct(
        ContratSponsorRepository $contratRepo,
        TwilioService $twilio
    ) {
        parent::__construct();
        $this->contratRepo = $contratRepo;
        $this->twilio = $twilio;
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run without sending actual SMS (test mode)')
            ->addOption('days-ahead', null, InputOption::VALUE_REQUIRED, 'Check for contracts expiring within N days', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Allow this command to run longer than PHP's default 30s limit
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $today = new \DateTime();
        $daysAhead = (int) $input->getOption('days-ahead');
        $dryRun = $input->getOption('dry-run');

        // Get expired contracts or contracts expiring soon
        if ($daysAhead > 0) {
            $contracts = $this->contratRepo->findExpiringWithinDays($daysAhead);
            $message = "Contrats expiring within {$daysAhead} days";
        } else {
            $contracts = $this->contratRepo->findByExpirationDate($today);
            $message = "Contrats expired today or earlier";
        }

        if (empty($contracts)) {
            $io->success("✅ No contracts found to process. ({$message})");
            return Command::SUCCESS;
        }

        $io->info("Found " . count($contracts) . " contract(s). ({$message})");
        $io->writeln('');

        $sentCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($contracts as $contrat) {
            $contractNumber = sprintf('#%d', $contrat->getId());
            $sponsorName = $contrat->getSponsor()?->getNom() ?? 'Unknown Sponsor';
            $equipePhone = $contrat->getEquipe()?->getTelephone();
            $expirationDate = $contrat->getDateFin()->format('d/m/Y');

            // Check if phone number exists
            if (!$equipePhone) {
                $io->warning("SKIP: No phone number for {$contractNumber} ({$sponsorName}) - Équipe: {$contrat->getEquipe()->getNom()}");
                $skippedCount++;
                continue;
            }

            // Prepare SMS message based on expiration status
            $today = new \DateTime();
            $contractEndDate = $contrat->getDateFin();
            
            if ($contractEndDate < $today) {
                // Contract is already expired
                $smsMessage = "Le contrat est expiré";
            } else {
                // Contract is expiring soon
                $smsMessage = sprintf(
                    "Alerte: Contrat %s avec %s expire le %s. Veuillez contacter l'administrateur.",
                    $contractNumber,
                    $sponsorName,
                    $expirationDate
                );
            }

            // Send SMS or simulate
            if ($dryRun) {
                $io->note("DRY-RUN: Would send SMS to {$equipePhone}: \"{$smsMessage}\"");
            } else {
                if ($this->twilio->sendSms($equipePhone, $smsMessage)) {
                    $io->success("SENT: SMS to {$equipePhone} for {$contractNumber} ({$sponsorName})");
                    $sentCount++;
                } else {
                    $io->error("FAILED: Could not send SMS to {$equipePhone} for {$contractNumber} ({$sponsorName})");
                    $failedCount++;
                }
            }
        }

        $io->writeln('');
        $io->info("Summary:");
        if ($dryRun) {
            $io->writeln("  • Dry-run mode: No actual SMS sent");
        } else {
            $io->writeln("  • SMS Sent: {$sentCount}");
            $io->writeln("  • SMS Failed: {$failedCount}");
        }
        $io->writeln("  • Skipped (no phone): {$skippedCount}");

        return Command::SUCCESS;
    }
}
