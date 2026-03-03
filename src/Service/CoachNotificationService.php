<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;

class CoachNotificationService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyCoach(User $coach, string $message): void
    {
        $email = (new Email())
            ->from('noreply@club.com')
            ->to($coach->getEmail())
            ->subject('Contrat sponsor expiré')
            ->text($message);

        $this->mailer->send($email);
    }
}
