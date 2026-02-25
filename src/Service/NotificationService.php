<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Entrainement;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $em,
        private \Psr\Log\LoggerInterface $logger
    ) {}

    public function notifyPlayerNewTraining(User $player, Entrainement $training): void
    {
        // ── 1. Notification en base de données ──────────────────────────
        $notif = new Notification();
        $notif->setUser($player);
        $notif->setMessage(
            "Nouvel entraînement de " . $training->getType()
            . " le " . $training->getDateEntrainement()->format('d/m/Y')
            . " à " . $training->getHeureDebut()->format('H:i')
            . " — " . $training->getLieu()
        );
        $notif->setCreatedAt(new \DateTime());
        $notif->setIsRead(false);

        $this->em->persist($notif);
        $this->em->flush();

        // ── 2. Email HTML riche ─────────────────────────────────────────
        $coach = $training->getEntraineur();
        $coachName = $coach ? $coach->getNomComplet() : 'Votre coach';

        $date      = $training->getDateEntrainement()->format('l d F Y');
        $heureD    = $training->getHeureDebut()->format('H:i');
        $heureF    = $training->getHeureFin()->format('H:i');
        $type      = htmlspecialchars($training->getType());
        $lieu      = htmlspecialchars($training->getLieu());
        $objectif  = nl2br(htmlspecialchars($training->getObjectif() ?? ''));
        $prenom    = htmlspecialchars($player->getPrenom());

        // Translate day name to French
        $joursFR = [
            'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche',
        ];
        $moisFR = [
            'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
            'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
            'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
            'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre',
        ];
        $dateFR = strtr($date, array_merge($joursFR, $moisFR));

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nouvel entraînement — Sport Insight</title>
</head>
<body style="margin:0; padding:0; background:#f0f7f2; font-family:'Segoe UI', Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f7f2; padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

          <!-- HEADER -->
          <tr>
            <td style="background:linear-gradient(135deg,#14532d 0%,#16a34a 60%,#22c55e 100%);
                        border-radius:16px 16px 0 0; padding:36px 40px; text-align:center;">
              <div style="font-size:48px; margin-bottom:10px;">🏋️</div>
              <h1 style="margin:0; color:#ffffff; font-size:28px; font-weight:800; letter-spacing:1px;">
                Nouvel Entraînement Planifié !
              </h1>
              <p style="margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px; letter-spacing:2px; text-transform:uppercase;">
                Sport Insight · Coach IA
              </p>
            </td>
          </tr>

          <!-- BODY -->
          <tr>
            <td style="background:#ffffff; padding:36px 40px;">

              <!-- Greeting -->
              <p style="margin:0 0 24px; font-size:17px; color:#1a2e22; font-weight:600;">
                Bonjour {$prenom} 👋
              </p>
              <p style="margin:0 0 28px; font-size:15px; color:#4b7060; line-height:1.7;">
                Un nouvel entraînement a été planifié pour vous. Voici tous les détails importants à retenir :
              </p>

              <!-- Training Card -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);
                      border:1px solid #bbf7d0; border-radius:14px; margin-bottom:28px; overflow:hidden;">
                <tr>
                  <td style="background:#16a34a; padding:14px 24px;">
                    <span style="color:#fff; font-size:18px; font-weight:800; text-transform:uppercase; letter-spacing:2px;">
                      🏟 {$type}
                    </span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:24px;">
                    <table width="100%" cellpadding="0" cellspacing="0">

                      <!-- Date -->
                      <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #d1fae5;">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="width:36px; font-size:22px;">📅</td>
                              <td>
                                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;">Date</div>
                                <div style="font-size:16px; font-weight:700; color:#14532d;">{$dateFR}</div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>

                      <!-- Horaire -->
                      <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #d1fae5;">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="width:36px; font-size:22px;">⏰</td>
                              <td>
                                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;">Horaire</div>
                                <div style="font-size:16px; font-weight:700; color:#14532d;">{$heureD} → {$heureF}</div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>

                      <!-- Lieu -->
                      <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #d1fae5;">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="width:36px; font-size:22px;">📍</td>
                              <td>
                                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;">Lieu</div>
                                <div style="font-size:16px; font-weight:700; color:#14532d;">{$lieu}</div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>

                      <!-- Coach -->
                      <tr>
                        <td style="padding:10px 0;">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="width:36px; font-size:22px;">🤝</td>
                              <td>
                                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77; font-weight:600;">Entraîneur</div>
                                <div style="font-size:16px; font-weight:700; color:#14532d;">{$coachName}</div>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>

                    </table>
                  </td>
                </tr>
              </table>

              <!-- Objectif -->
              <div style="background:#f8fdf9; border-left:4px solid #16a34a; border-radius:0 10px 10px 0;
                          padding:18px 20px; margin-bottom:28px;">
                <div style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#6b8f77;
                            font-weight:700; margin-bottom:8px;">🎯 Objectif de la séance</div>
                <div style="font-size:15px; color:#1a2e22; line-height:1.7;">{$objectif}</div>
              </div>

              <!-- CTA hint -->
              <p style="margin:0; font-size:14px; color:#6b8f77; line-height:1.7; text-align:center;">
                Connectez-vous à votre espace <strong style="color:#16a34a;">Sport Insight</strong>
                pour confirmer votre participation.
              </p>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="background:#f0fdf4; border-top:1px solid #d1fae5; border-radius:0 0 16px 16px;
                        padding:20px 40px; text-align:center;">
              <p style="margin:0; font-size:12px; color:#6b8f77;">
                © 2025 Sport Insight · Ce message a été envoyé automatiquement. Ne pas répondre.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
HTML;

        $email = (new Email())
            ->from('sportinsight.contact@gmail.com')
            ->to($player->getEmail())
            ->subject("🏋️ Nouvel entraînement {$type} — {$dateFR}")
            ->html($html);

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Email error: ' . $e->getMessage());
        }
    }
}