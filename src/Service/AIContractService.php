<?php

namespace App\Service;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIContractService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Generate a detailed sponsorship contract text using OpenAI API.
     * Returns the generated text (string) or an empty string on failure.
     *
     * @param string $sponsor
     * @param string $equipe
     * @param int    $duree   Duration in months
     * @param float  $montant Amount in TND
     * @return string
     */
    public function generateContract(string $sponsor, string $equipe, int $duree, float $montant): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            return '';
        }

        $prompt = <<<PROMPT
Rédige un contrat de sponsoring professionnel très détaillé entre $sponsor et $equipe.
Durée : $duree mois. Montant : $montant TND.

Inclure OBLIGATOIREMENT avec DÉTAILS :

Article 1 – Objet : partenariat sponsoring, objectifs, visibilité proportionnée
Article 2 – Obligations du Sponsor : versement $montant TND, respect éthique, relation professionnelle
Article 3 – Visibilité de l'Équipe : panneaux stade, maillots avec logo (poitrine/manche/dos), 
           réseaux sociaux, mentions médias, droits image, documents officiels
Article 4 – Modalités de Paiement : échéancier détaillé (ex: 50% signature, 25% mi-période, 25% fin), 
           pénalités retard 1% par mois, mode de paiement
Article 5 – Durée : $duree mois précis, conditions renouvellement, préavis 60 jours
Article 6 – Résiliation : préavis 30 jours, indemnités si résiliation anticipée (50% restant dû)
Article 7 – Litiges : résolution amiable 30j, tribunaux Tunis, loi tunisienne

Style : juridique formel, clairs, numérotation stricte, énumérations avec tirets
PROMPT;

        try {
            $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ],
            ]);

            $data = $response->toArray();
            return $data['choices'][0]['message']['content'] ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Build a professional HTML contract with header, numbered articles, signature, and QR code.
     * - $logoPath and $signaturePath are absolute filesystem paths.
     * - These paths are converted to file:// URLs for Dompdf rendering.
     * - If Endroid QR lib is available this will generate a QR image under `public/uploads/qr/`.
     *
     * @param string      $sponsor
     * @param string      $equipe
     * @param int         $duree
     * @param float       $montant
     * @param string|null $logoPath          Absolute filesystem path to logo
     * @param string|null $signaturePath     Absolute filesystem path to signature
     * @param array       $sponsorContact    Sponsor contact info (address, phone, email)
     * @param array       $equipeContact     Team contact info (address, phone, email)
     * @return string HTML
     */
    public function generateContractHtml(string $sponsor, string $equipe, int $duree, float $montant, ?string $logoPath = null, ?string $signaturePath = null, array $sponsorContact = [], array $equipeContact = []): string
    {
        $contractText = $this->generateContract($sponsor, $equipe, $duree, $montant);

        // Safe fallback if AI returned nothing
        if (empty(trim($contractText))) {
            $contractText = $this->generateFallbackContract($sponsor, $equipe, $duree, $montant, $sponsorContact, $equipeContact);
        }

        // Prepare logo HTML with absolute file:// path
        $logoHtml = '';
        if ($logoPath && file_exists($logoPath)) {
            // Convert Windows backslashes to forward slashes for file:// URLs
            $fileLogoPath = 'file://' . str_replace('\\', '/', $logoPath);
            $logoHtml = sprintf('<img src="%s" alt="%s" width="200" height="100" style="max-width:300px; height:auto;">', htmlspecialchars($fileLogoPath, ENT_QUOTES), htmlspecialchars($sponsor, ENT_QUOTES));
        }

        // Prepare signature HTML with absolute file:// path
        $signatureHtml = '';
        if ($signaturePath && file_exists($signaturePath)) {
            // Convert Windows backslashes to forward slashes for file:// URLs
            $fileSignaturePath = 'file://' . str_replace('\\', '/', $signaturePath);
            $signatureHtml = sprintf('<img src="%s" alt="Signature" width="200" height="80" style="max-width:300px; height:auto;">', htmlspecialchars($fileSignaturePath, ENT_QUOTES));
        }

        // Attempt to generate QR code if endroid/qr-code is installed
        $qrHtml = '';
        try {
            if (class_exists('\Endroid\QrCode\Builder\Builder')) {
                $qrDir = __DIR__ . '/../../public/uploads/qr';
                if (!is_dir($qrDir)) {
                    @mkdir($qrDir, 0755, true);
                }

                $qrFilename = uniqid('qr_') . '.png';
                $qrPathFilesystem = $qrDir . '/' . $qrFilename;
                $qrPathPublic = '/uploads/qr/' . $qrFilename;

                // Build a simple QR payload: sponsor|equipe|montant
                $payload = sprintf('sponsor:%s|equipe:%s|montant:%.2f', $sponsor, $equipe, $montant);

                $qrCode = new QrCode(
                    data: $payload,
                    size: 300,
                    margin: 10
                );

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $result = $result->getString();
                file_put_contents($qrPathFilesystem, $result);

                $qrHtml = sprintf('<img src="%s" alt="QR Code" style="height:100px;">', htmlspecialchars($qrPathPublic, ENT_QUOTES));
            }
        } catch (\Throwable $e) {
            // If QR generation fails, continue without it
            $qrHtml = '';
        }

        // Professional HTML structure with improved styling for Dompdf
        $html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrat de Sponsoring</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            color: #1a1a1a;
            line-height: 1.7;
            padding: 30px 30px;
            background: #fff;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
        }
        .sponsor-info {
            font-size: 9pt;
            color: #555;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .logo {
            margin-bottom: 15px;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo img {
            max-height: 80px;
            width: auto;
            display: block;
        }
        .title h1 {
            font-size: 20pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0;
            letter-spacing: 1px;
        }
        .content {
            margin: 20px 0;
        }
        .preamble {
            margin-bottom: 20px;
            text-align: justify;
        }
        .article {
            margin: 12px 0 12px 0;
            page-break-inside: avoid;
        }
        .article-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
            margin-left: 20px;
            text-indent: -20px;
        }
        .article-content {
            margin-left: 20px;
            text-align: justify;
            line-height: 1.6;
        }
        .article-content ul {
            margin-left: 20px;
            margin-top: 5px;
        }
        .article-content li {
            margin: 3px 0;
        }
        .signature-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
        }
        .signature-row {
            margin-top: 30px;
            text-align: center;
        }
        .signature-col {
            display: inline-block;
            width: 32%;
            margin: 0 1%;
            text-align: center;
            vertical-align: top;
        }
        .signature-image {
            height: 70px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signature-image img {
            max-height: 60px;
            width: auto;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 3px;
            font-size: 9pt;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10pt;
            color: #333;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .footer-number {
            font-weight: bold;
            margin: 10px 0;
        }
        .footer-date {
            font-size: 9pt;
            color: #666;
            margin: 5px 0;
        }
        .qr-code {
            margin-top: 20px;
            text-align: center;
        }
        .qr-code img {
            height: 100px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="sponsor-info">
            SPONSOR_CONTACT_PLACEHOLDER
        </div>
        <div class="logo">
            LOGO_PLACEHOLDER
        </div>
        <div class="title">
            <h1>CONTRAT DE SPONSORING</h1>
        </div>
    </div>

    <div class="content">
        CONTENT_PLACEHOLDER
    </div>

    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-col">
                <strong>Sponsor</strong>
                <div class="signature-image">
                    SPONSOR_SIGNATURE
                </div>
                <div class="signature-line">Signature et Cachet</div>
            </div>
            <div class="signature-col">
                <strong>Représentant Équipe</strong>
                <div class="signature-image">
                    (Signature)
                </div>
                <div class="signature-line">Signature</div>
            </div>
            <div class="signature-col">
                <strong>Témoin/Intermédiaire</strong>
                <div class="signature-image">
                    (Signature)
                </div>
                <div class="signature-line">Signature</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-number">
            FOOTER_PLACEHOLDER
        </div>
        <div class="qr-code">
            QR_CODE_PLACEHOLDER
        </div>
        <div class="footer-date">
            EQUIPE_CONTACT_PLACEHOLDER
        </div>
    </div>
</body>
</html>
HTML;

        // Replace placeholders
        $sponsorContactStr = htmlspecialchars($sponsor, ENT_QUOTES);
        if (!empty($sponsorContact['address'])) {
            $sponsorContactStr .= '<br>' . htmlspecialchars($sponsorContact['address'], ENT_QUOTES);
        }
        if (!empty($sponsorContact['phone'])) {
            $sponsorContactStr .= '<br>Tél : ' . htmlspecialchars($sponsorContact['phone'], ENT_QUOTES);
        }
        if (!empty($sponsorContact['email'])) {
            $sponsorContactStr .= '<br>Email : ' . htmlspecialchars($sponsorContact['email'], ENT_QUOTES);
        }

        $equipeContactStr = htmlspecialchars($equipe, ENT_QUOTES);
        if (!empty($equipeContact['address'])) {
            $equipeContactStr .= '<br>' . htmlspecialchars($equipeContact['address'], ENT_QUOTES);
        }
        if (!empty($equipeContact['phone'])) {
            $equipeContactStr .= '<br>Tél : ' . htmlspecialchars($equipeContact['phone'], ENT_QUOTES);
        }
        if (!empty($equipeContact['email'])) {
            $equipeContactStr .= '<br>Email : ' . htmlspecialchars($equipeContact['email'], ENT_QUOTES);
        }

        $html = str_replace('SPONSOR_CONTACT_PLACEHOLDER', $sponsorContactStr, $html);
        $html = str_replace('EQUIPE_CONTACT_PLACEHOLDER', $equipeContactStr, $html);
        $html = str_replace('LOGO_PLACEHOLDER', $logoHtml ?: '<em>Logo non disponible</em>', $html);
        $html = str_replace('CONTENT_PLACEHOLDER', $this->formatContractContent($contractText), $html);
        $html = str_replace('SPONSOR_SIGNATURE', $signatureHtml ?: '<em>Signature</em>', $html);
        $html = str_replace('QR_CODE_PLACEHOLDER', $qrHtml ?: '', $html);

        return $html;
    }

    /**
     * Format contract content with article structure and list support.
     * Uses a simple line-by-line parser to handle articles, texts, and bullet points.
     *
     * @param string $contractText
     * @return string HTML
     */
    private function formatContractContent(string $contractText): string
    {
        $lines = explode("\n", $contractText);
        $html = '';
        $currentArticle = null;
        $currentContent = [];
        $inList = false;
        $currentList = [];
        
        foreach ($lines as $idx => $line) {
            $trimmed = trim($line);
            
            // Check if this line starts an article
            if (preg_match('/^Article\s+(\d+)\s*[-–—]?\s*(.*)$/i', $trimmed, $matches)) {
                // Save previous content
                if (!empty($currentContent) || !empty($currentList)) {
                    if ($currentArticle !== null) {
                        $content = $this->formatArticleBody($currentContent, $currentList);
                        $html .= sprintf(
                            '<div class="article"><div class="article-title">Article %d</div><div class="article-content">%s</div></div>',
                            $currentArticle,
                            $content
                        );
                    } else {
                        // Preamble
                        $html .= '<div class="preamble">' . nl2br(htmlspecialchars(implode("\n", $currentContent), ENT_QUOTES)) . '</div>';
                    }
                }
                
                // Start new article
                $currentArticle = intval($matches[1]);
                $currentContent = [];
                $currentList = [];
                $inList = false;
                
                // Add title content if present
                if (!empty($matches[2])) {
                    $currentContent[] = htmlspecialchars($matches[2], ENT_QUOTES);
                }
            } elseif ($currentArticle !== null && preg_match('/^[-•]\s+(.+)$/', $trimmed, $listMatches)) {
                // List item
                $inList = true;
                $currentList[] = htmlspecialchars($listMatches[1], ENT_QUOTES);
            } elseif ($currentArticle !== null && !empty($trimmed)) {
                // Regular content for article
                if ($inList && !empty($currentList)) {
                    // End of list, save it
                    $currentContent[] = '<ul><li>' . implode('</li><li>', $currentList) . '</li></ul>';
                    $currentList = [];
                    $inList = false;
                }
                $currentContent[] = htmlspecialchars($trimmed, ENT_QUOTES);
            } elseif ($currentArticle === null && !empty($trimmed)) {
                // Preamble content
                $currentContent[] = htmlspecialchars($trimmed, ENT_QUOTES);
            }
        }
        
        // Save last article/content
        if (!empty($currentContent) || !empty($currentList)) {
            if ($currentArticle !== null) {
                $content = $this->formatArticleBody($currentContent, $currentList);
                $html .= sprintf(
                    '<div class="article"><div class="article-title">Article %d</div><div class="article-content">%s</div></div>',
                    $currentArticle,
                    $content
                );
            } else {
                // Preamble
                $html .= '<div class="preamble">' . nl2br(implode('<br>', $currentContent)) . '</div>';
            }
        }
        
        return !empty($html) ? $html : '<p>' . nl2br(htmlspecialchars($contractText, ENT_QUOTES)) . '</p>';
    }
    
    /**
     * Format article body with lists and text.
     *
     * @param array $content
     * @param array $list
     * @return string HTML
     */
    private function formatArticleBody(array $content, array $list): string
    {
        $result = implode('<br>', $content);
        
        if (!empty($list)) {
            $result .= '<ul><li>' . implode('</li><li>', $list) . '</li></ul>';
        }
        
        return $result;
    }

    /**
     * Generate a default contract structure when AI fails.
     * Provides a detailed, professional fallback with rich content.
     *
     * @param string $sponsor
     * @param string $equipe
     * @param int    $duree
     * @param float  $montant
     * @param array  $sponsorContact
     * @param array  $equipeContact
     * @return string
     */
    private function generateFallbackContract(string $sponsor, string $equipe, int $duree, float $montant, array $sponsorContact = [], array $equipeContact = []): string
    {
        $now = new \DateTime();
        $end = (clone $now)->modify("+{$duree} months");
    {
        // Ensure duree is realistic
        if ($duree <= 0) {
            $duree = 12; // Default to 12 months if invalid
        }

        $now = new \DateTime();
        $end = (clone $now)->modify("+{$duree} months");
        $midDate = (clone $now)->modify('+' . ceil($duree/2) . ' months');

        // Format contact information
        $sponsorInfo = htmlspecialchars($sponsor, ENT_QUOTES);
        if (!empty($sponsorContact['address'])) {
            $sponsorInfo .= ' | ' . htmlspecialchars($sponsorContact['address'], ENT_QUOTES);
        }
        if (!empty($sponsorContact['phone'])) {
            $sponsorInfo .= ' | Tél : ' . htmlspecialchars($sponsorContact['phone'], ENT_QUOTES);
        }
        if (!empty($sponsorContact['email'])) {
            $sponsorInfo .= ' | ' . htmlspecialchars($sponsorContact['email'], ENT_QUOTES);
        }

        $equipeInfo = htmlspecialchars($equipe, ENT_QUOTES);
        if (!empty($equipeContact['address'])) {
            $equipeInfo .= ' | ' . htmlspecialchars($equipeContact['address'], ENT_QUOTES);
        }
        if (!empty($equipeContact['phone'])) {
            $equipeInfo .= ' | Tél : ' . htmlspecialchars($equipeContact['phone'], ENT_QUOTES);
        }
        if (!empty($equipeContact['email'])) {
            $equipeInfo .= ' | ' . htmlspecialchars($equipeContact['email'], ENT_QUOTES);
        }

        $montantWords = $this->numberToWords($montant);
        $half = $montant * 0.5;
        $quarter = $montant * 0.25;

        return "PRÉAMBULE\n\n" .
            "Entre $sponsorInfo, ci-après dénommée « le Sponsor »,\n" .
            "Et $equipeInfo, ci-après dénommée « l'Équipe »,\n\n" .
            "Les parties reconnaissent leur volonté d'établir un partenariat de sponsoring professionnel et contractuel.\n\n" .
            "ARTICLE 1 – OBJET DU CONTRAT\n\n" .
            "Le présent contrat établit une relation de sponsoring entre le Sponsor et l'Équipe. Le Sponsor finance le club/équipe en contrepartie d'une visibilité commerciale et médiatique. La période d'engagement s'étend sur $duree mois, du " . $now->format('d/m/Y') . " au " . $end->format('d/m/Y') . ". Chaque partie s'engage à respecter intégralement les obligations stipulées ci-après.\n\n" .
            "ARTICLE 2 – OBLIGATIONS FINANCIÈRES DU SPONSOR\n\n" .
            "Le Sponsor s'engage formellement à :\n" .
            "- Verser le montant total de $montant TND, soit $montantWords dinars tunisiens\n" .
            "- Versements échelonnés comme suit : 1) 50% ($half TND) à la signature ; 2) 25% ($quarter TND) le " . $midDate->format('d/m/Y') . " ; 3) 25% ($quarter TND) le " . $end->format('d/m/Y') . "\n" .
            "- Mode de paiement : virement bancaire sur compte courant de l'Équipe\n" .
            "- En cas de retard supérieur à 30 jours, une pénalité de 1% par mois s'ajoute au montant dû\n" .
            "- Respecter l'intégrité morale, éthique et la réputation sportive de l'Équipe\n" .
            "- Autoriser l'Équipe à poursuivre ses activités commerciales parallèles\n\n" .
            "ARTICLE 3 – OBLIGATIONS DE VISIBILITÉ DE L'ÉQUIPE\n\n" .
            "L'Équipe s'engage à accorder au Sponsor une visibilité maximale et continue :\n" .
            "- Affichage des panneaux publicitaires : mur arrière, mur latéral, zone d'accueil, zones VIP\n" .
            "- Port obligatoire des maillots avec logo du Sponsor : poitrine (20x20cm), manche (10x10cm), dos\n" .
            "- Mentions du Sponsor dans tous les documents officiels, newsletters et communications\n" .
            "- Promotion sur réseaux sociaux : hashtags officiels, mentions d'événements, partage des posts\n" .
            "- Présence du logo en conférences de presse et événements publics\n" .
            "- Cession des droits d'image : l'Équipe autorise le Sponsor à utiliser photos, vidéos à titre gratuit\n\n" .
            "ARTICLE 4 – MODALITÉS DE PAIEMENT DÉTAILLÉES\n\n" .
            "Calendrier Précis :\n" .
            "- 1er versement : 50% ($half TND) lors de la signature, non remboursable sauf force majeure\n" .
            "- 2e versement : 25% ($quarter TND) le " . $midDate->format('d/m/Y') . "\n" .
            "- 3e versement : 25% ($quarter TND) le " . $end->format('d/m/Y') . "\n" .
            "- Mode de paiement : virement bancaire\n" .
            "- Pénalité retard : 1% par mois + majoration 5% après 60 jours\n" .
            "- Aucun remboursement partiel n'est accordé après le 1er versement\n\n" .
            "ARTICLE 5 – DURÉE DU CONTRAT\n\n" .
            "- Durée : $duree mois, du " . $now->format('d/m/Y') . " au " . $end->format('d/m/Y') . "\n" .
            "- Le contrat expire automatiquement à la fin de cette période\n" .
            "- Renouvellement : possible uniquement par écrit, avec préavis de 60 jours\n" .
            "- En cas de continuation, les conditions s'appliquent à titre de prorogation\n\n" .
            "ARTICLE 6 – RÉSILIATION ANTICIPÉE ET INDEMNITÉS\n\n" .
            "Motifs de Résiliation :\n" .
            "- Non-respect grave des obligations par l'une des parties (préavis 30 jours)\n" .
            "- Événement de force majeure affectant significativement le partenariat\n" .
            "- Atteinte grave à la réputation d'une des parties\n" .
            "Indemnités :\n" .
            "- Si résiliation par le Sponsor : compensation de 50% du montant restant dû\n" .
            "- Si résiliation par l'Équipe : perte forfaitaire de 50% des droits acquis\n\n" .
            "ARTICLE 7 – LITIGES ET JURIDICTION\n\n" .
            "- Résolution amiable : négociation 30 jours minimum avant action judiciaire\n" .
            "- Juridiction compétente : Tribunal de Première Instance de Tunis\n" .
            "- Loi applicable : législation tunisienne\n" .
            "- Confidentialité du contrat sauf obligation légale\n\n" .
            "Fait à Tunis, le " . $now->format('d/m/Y');
    }
    }

    /**
     * Convert number to words in French (for contract amounts).
     * Simple implementation for reasonable monetary values.
     *
     * @param float $amount
     * @return string
     */
    private function numberToWords(float $amount): string
    {
        $intPart = (int) $amount;
        
        // Simple mapping for common numbers
        $ones = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingts', 'quatre-vingt-dix'];
        
        if ($intPart < 10) {
            return $ones[$intPart];
        } elseif ($intPart < 20) {
            return $tens[1] . ($intPart > 10 ? '-' . $ones[$intPart - 10] : '');
        } elseif ($intPart < 100) {
            $tens_digit = (int)($intPart / 10);
            $ones_digit = $intPart % 10;
            return $tens[$tens_digit] . ($ones_digit > 0 ? '-' . $ones[$ones_digit] : '');
        } else {
            // For simplicity, return the numeric value for larger amounts
            return number_format($amount, 2, ',', ' ');
        }
    }
}
