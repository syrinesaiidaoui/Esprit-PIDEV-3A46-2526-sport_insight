<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ModerationService
{
    private $projectDir;
    private $httpClient;
    private $params;

    public function __construct(ParameterBagInterface $params, \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient)
    {
        $this->projectDir = $params->get('kernel.project_dir');
        $this->httpClient = $httpClient;
        $this->params = $params;
    }

    public function verifyFace(string $capturedBase64, string $referencePath): array
    {
        // Simulation mode check
        if ($this->params->has('app.face_recognition_simulation') && $this->params->get('app.face_recognition_simulation')) {
            return [
                'verified' => true,
                'confidence' => 99.9,
                'threshold' => 0.6,
                'message' => 'Identité vérifiée (MODE SIMULATION)',
            ];
        }

        try {
            // Local FastAPI endpoint (running via scripts/face_api.py)
            $apiUrl = 'http://127.0.0.1:8001/verify';

            // Check if service is up (optional, but good for error messaging)
            $response = $this->httpClient->request('POST', $apiUrl, [
                'body' => [
                    'captured_image_base64' => $capturedBase64,
                    'reference_image_path' => $referencePath,
                ],
            ]);

            $data = $response->toArray();

            return [
                'verified' => $data['verified'] ?? false,
                'confidence' => isset($data['distance']) ? (1 - $data['distance']) * 100 : 0, // Mock confidence
                'threshold' => isset($data['threshold']) ? $data['threshold'] : 0,
                'error' => $data['error'] ?? null,
                'message' => ($data['verified'] ?? false) ? 'Identité vérifiée via local AI' : ($data['error'] ?? 'Visage non reconnu'),
            ];
        } catch (\Exception $e) {
            // Check for connection error to suggest starting the Python service
            $errorMsg = $e->getMessage();
            if (str_contains($errorMsg, '127.0.0.1:8001') || str_contains($errorMsg, 'localhost:8001')) {
                return [
                    'verified' => false,
                    'error' => 'Le service de reconnaissance faciale est hors ligne. (Avez-vous lancé "python scripts/face_api.py" ?)',
                ];
            }

            return [
                'verified' => false,
                'error' => 'Erreur technique : ' . $errorMsg,
            ];
        }
    }

    public function checkComment(string $text): array
    {
        $pythonPath = 'python';
        $scriptPath = $this->projectDir . '/scripts/moderator.py';

        $process = new Process([$pythonPath, $scriptPath, $text]);
        $process->run();

        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $result = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
        }

        // --- INTERNAL PHP FALLBACK (If Python fails or is missing) ---
        return $this->fallbackCheck($text);
    }

    private function fallbackCheck(string $text): array
    {
        $textLower = mb_strtolower(trim($text));

        $forbidden = [
            'insulte' => ['idiot', 'debile', 'con', 'salaud', 'merde', 'pute', 'encule', 'foufou', 'connard', 'salope'],
            'toxicity' => ['tuer', 'mort', 'haine', 'deteste', 'raciste', 'nazi', 'violence', 'suicide', 'sang', 'menace'],
            'spam' => ['viagra', 'casino', 'gagner argent', 'cliquez ici', 'sexy', 'gratuit', 'vendre', 'achat', 'promo']
        ];

        // Shouting check
        if (strlen($text) > 5 && strtoupper($text) === $text && !preg_match('/[a-z]/', $text)) {
            return [
                'status' => 'BLOCKED',
                'reason' => 'Toxicity (Shouting detected by fallback)',
                'cleanedText' => $text
            ];
        }

        foreach ($forbidden as $category => $words) {
            foreach ($words as $word) {
                // Match with character repetition (e.g. "morrt" -> "mort")
                $pattern = '/';
                for ($i = 0; $i < mb_strlen($word); $i++) {
                    $pattern .= preg_quote(mb_substr($word, $i, 1), '/') . '+';
                }
                $pattern .= '/iu';

                if (preg_match($pattern, $textLower)) {
                    return [
                        'status' => 'BLOCKED',
                        'reason' => "Catégorie détectée: " . ucfirst($category) . " (Mot: $word - Regex PHP)",
                        'cleanedText' => $text
                    ];
                }
            }
        }

        return [
            'status' => 'APPROVED',
            'reason' => 'Safe content (Validated by fallback PHP)',
            'cleanedText' => $text
        ];
    }
}
