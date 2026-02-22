<?php

namespace App\Controller\BackOffice;

use App\Entity\ContratSponsor;
use App\Service\AIContractService;
use App\Service\TwilioService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contrat')]
final class ContractGeneratorController extends AbstractController
{

    #[Route('/{id}/generate_pdf', name: 'app_contrat_sponsor_generate_pdf', methods: ['GET'])]
    public function generatePdf(ContratSponsor $contrat, AIContractService $aiService): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        // Prepare absolute paths for logo and signature
        $logoPath = null;
        if ($contrat->getSponsor()?->getLogoName()) {
            $candidatePath = $projectDir . '/public/uploads/logos/' . $contrat->getSponsor()->getLogoName();
            if (file_exists($candidatePath)) {
                $logoPath = $candidatePath;
            }
        }
        // Fallback to default logo if sponsor doesn't have one
        if (!$logoPath) {
            $defaultLogo = $projectDir . '/public/uploads/logos/logo.png';
            if (file_exists($defaultLogo)) {
                $logoPath = $defaultLogo;
            }
        }

        $signaturePath = $projectDir . '/public/uploads/signatures/sign.png';
        if (!file_exists($signaturePath)) {
            $signaturePath = null;
        }

        // Extract sponsor and team contact information
        $sponsorContact = [];
        if ($sponsor = $contrat->getSponsor()) {
            $sponsorContact['address'] = $sponsor->getAdresse() ?? '';
            $sponsorContact['phone'] = $sponsor->getTelephone() ?? '';
            $sponsorContact['email'] = $sponsor->getEmail() ?? '';
        }

        $equipeContact = [];
        if ($equipe = $contrat->getEquipe()) {
            $equipeContact['address'] = $equipe->getAdresse() ?? '';
            $equipeContact['phone'] = $equipe->getTelephone() ?? '';
            $equipeContact['email'] = $equipe->getEmail() ?? '';
        }

        // Generate HTML using AI service (this may call external API)
        $html = $aiService->generateContractHtml(
            $contrat->getSponsor()?->getNom() ?? 'Sponsor',
            $contrat->getEquipe()?->getNom() ?? 'Équipe',
            (int) max(1, $contrat->getDateFin()->diff($contrat->getDateDebut())->m + 12 * $contrat->getDateFin()->diff($contrat->getDateDebut())->y),
            (float) $contrat->getMontant(),
            $logoPath,
            $signaturePath,
            $sponsorContact,
            $equipeContact
        );

        // Add unique contract number and generation date
        $number = 'CTR-' . (new \DateTime())->format('Ymd') . '-' . random_int(1000, 9999);
        $date = (new \DateTime())->format('d/m/Y');
        $location = 'Tunis'; // You can make this configurable

        // Replace footer placeholder with actual number and date
        $footerContent = sprintf(
            "Numéro de Contrat : <strong>%s</strong><br/>Date de Signature : %s<br/>Lieu : %s",
            htmlspecialchars($number, ENT_QUOTES),
            htmlspecialchars($date, ENT_QUOTES),
            htmlspecialchars($location, ENT_QUOTES)
        );
        $html = str_replace('FOOTER_PLACEHOLDER', $footerContent, $html);

        // Dompdf requires absolute paths for images; rewrite /uploads/... to file:// paths
        $publicUploadsPath = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $html = str_replace('/uploads/', 'file://' . str_replace('\\', '/', $publicUploadsPath), $html);

        // Configure Dompdf
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        $filename = sprintf('contrat_%s.pdf', $number);

        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    #[Route('/test-sms', name: 'app_test_sms', methods: ['GET'])]
    public function testSms(TwilioService $twilio): Response
    {
        // Test SMS with your personal phone number
        $success = $twilio->sendSms(
            '+21693677092', // Replace with your validated Twilio phone number
            "Test Twilio : ton contrat CTR-20260222-4014 a été généré avec succès."
        );

        $message = $success 
            ? "✅ SMS envoyé avec succès !" 
            : "❌ Erreur lors de l'envoi du SMS. Vérifiez les logs.";

        return new Response($message, $success ? 200 : 500);
    }
}
