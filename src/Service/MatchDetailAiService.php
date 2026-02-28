<?php

namespace App\Service;

use App\Entity\Matchs;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MatchDetailAiService
{
    private const DEFAULT_MODEL = 'llama-3.3-70b-versatile';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $groqApiKey = ''
    ) {
    }

    public function replyToMatchQuestion(Matchs $match, string $userMessage): string
    {
        $question = trim($userMessage);
        if ($question === '') {
            return 'Veuillez saisir un message.';
        }

        if (!$this->isMatchRelatedQuestion($match, $question)) {
            return $this->buildOutOfScopeReply($match);
        }

        if (!$this->hasConfiguredApiKey()) {
            return 'Le service IA n est pas configure. Ajoutez GROQ_API_KEY dans le fichier .env.local.';
        }

        $outOfScopeReply = $this->buildOutOfScopeReply($match);
        $context = $this->buildMatchContext($match);
        $model = $_ENV['GROQ_MODEL'] ?? getenv('GROQ_MODEL') ?: self::DEFAULT_MODEL;

        $systemPrompt = <<<PROMPT
You are the Sport Insight assistant for one single football match.
Rules:
1) Answer only about the exact match in MATCH_CONTEXT.
2) If the user asks anything outside this match, answer exactly:
{$outOfScopeReply}
3) Never invent facts. If missing data, reply: Information non disponible dans le detail du match.
4) Keep answers concise, plain text, in French.
PROMPT;

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->groqApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => $model,
                        'temperature' => 0.2,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            [
                                'role' => 'user',
                                'content' => "MATCH_CONTEXT:\n{$context}\n\nQUESTION:\n{$question}",
                            ],
                        ],
                    ],
                    'timeout' => 20,
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $raw = $response->getContent(false);
                    $decoded = json_decode($raw, true);
                    $apiError = trim((string)($decoded['error']['message'] ?? ''));

                    if ($apiError !== '') {
                        $this->logger->warning('Groq non-200 response', [
                            'status' => $statusCode,
                            'error' => $apiError,
                        ]);
                    } else {
                        $this->logger->warning('Groq non-200 response without message', [
                            'status' => $statusCode,
                        ]);
                    }

                    if ($statusCode === 429) {
                        return 'Quota Groq atteinte. Verifiez votre billing et vos limites API.';
                    }

                    if ($statusCode === 401 || $statusCode === 403) {
                        return 'Cle Groq invalide ou non autorisee.';
                    }

                    $fallbackReply = $this->buildDeterministicReply($match, $question);
                    if ($fallbackReply !== null) {
                        return $fallbackReply;
                    }

                    return 'Le service IA est temporairement indisponible.';
                }

                $payload = $response->toArray(false);
                $reply = trim((string)($payload['choices'][0]['message']['content'] ?? ''));

                if ($reply === '') {
                    return 'Information non disponible dans le detail du match.';
                }

                return $reply;
            } catch (TransportExceptionInterface $exception) {
                $this->logger->warning('Groq transport error', [
                    'attempt' => $attempt,
                    'error' => $exception->getMessage(),
                ]);

                if ($attempt < 2) {
                    usleep(250000);
                    continue;
                }
            } catch (\Throwable $exception) {
                $this->logger->error('Groq request failed', [
                    'error' => $exception->getMessage(),
                ]);
                break;
            }
        }

        $fallbackReply = $this->buildDeterministicReply($match, $question);
        if ($fallbackReply !== null) {
            return $fallbackReply;
        }

        return 'Le service IA est temporairement indisponible.';
    }

    private function isMatchRelatedQuestion(Matchs $match, string $message): bool
    {
        $question = $this->normalize($message);
        if ($question === '') {
            return false;
        }

        $homeTeam = $this->normalize((string)($match->getEquipeDomicile()?->getNom() ?? ''));
        $awayTeam = $this->normalize((string)($match->getEquipeExterieur()?->getNom() ?? ''));
        if ($homeTeam !== '' && str_contains($question, $homeTeam)) {
            return true;
        }
        if ($awayTeam !== '' && str_contains($question, $awayTeam)) {
            return true;
        }

        $keywords = [
            'match',
            'score',
            'resultat',
            'date',
            'heure',
            'statut',
            'type',
            'lieu',
            'stade',
            'domicile',
            'exterieur',
            'equipe',
            'equipes',
            'joueur',
            'joueurs',
            'composition',
            'formation',
            'buteur',
            'but',
            'carton',
            'penalty',
            'coach',
            'entraineur',
            'titulaire',
            'remplacant',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($question, $keyword)) {
                return true;
            }
        }

        $players = [];
        foreach ($match->getMatchLineups() as $lineup) {
            $player = $lineup->getJoueur();
            if ($player === null) {
                continue;
            }
            $players[] = $this->normalize((string)$player->getNom());
            $players[] = $this->normalize((string)$player->getPrenom());
        }

        foreach (array_filter($players) as $playerWord) {
            if (strlen($playerWord) >= 3 && str_contains($question, $playerWord)) {
                return true;
            }
        }

        return false;
    }

    private function buildOutOfScopeReply(Matchs $match): string
    {
        $home = trim((string)($match->getEquipeDomicile()?->getNom() ?? 'Equipe domicile'));
        $away = trim((string)($match->getEquipeExterieur()?->getNom() ?? 'Equipe exterieur'));

        return sprintf('Desole, je reponds uniquement au detail du match %s vs %s.', $home, $away);
    }

    private function buildMatchContext(Matchs $match): string
    {
        $homeTeam = (string)($match->getEquipeDomicile()?->getNom() ?? 'N/A');
        $awayTeam = (string)($match->getEquipeExterieur()?->getNom() ?? 'N/A');
        $date = $match->getDateMatch()?->format('d/m/Y') ?? 'N/A';
        $time = $match->getHeureDebut()?->format('H:i') ?? 'N/A';
        $location = (string)($match->getLieu() ?? 'N/A');
        $type = (string)($match->getType() ?? 'N/A');
        $status = (string)($match->getStatut() ?? 'N/A');
        $scoreHome = (string)($match->getScoreEquipeDomicile() ?? 0);
        $scoreAway = (string)($match->getScoreEquipeExterieur() ?? 0);

        $homePlayers = [];
        $awayPlayers = [];
        foreach ($match->getMatchLineups() as $lineup) {
            $player = $lineup->getJoueur();
            if ($player === null) {
                continue;
            }

            $entry = sprintf(
                '#%s %s %s',
                (string)($player->getNumero() ?? '?'),
                trim((string)$player->getPrenom()),
                trim((string)$player->getNom())
            );

            if ($lineup->getType() === 'domicile') {
                $homePlayers[] = $entry;
            } elseif ($lineup->getType() === 'exterieur') {
                $awayPlayers[] = $entry;
            }
        }

        $lines = [
            'EQUIPE_DOMICILE: ' . $homeTeam,
            'EQUIPE_EXTERIEUR: ' . $awayTeam,
            'DATE: ' . $date,
            'HEURE: ' . $time,
            'LIEU: ' . $location,
            'TYPE: ' . $type,
            'STATUT: ' . $status,
            'SCORE: ' . $scoreHome . ' - ' . $scoreAway,
            'JOUEURS_DOMICILE: ' . (!empty($homePlayers) ? implode(', ', $homePlayers) : 'Aucune information'),
            'JOUEURS_EXTERIEUR: ' . (!empty($awayPlayers) ? implode(', ', $awayPlayers) : 'Aucune information'),
        ];

        return implode("\n", $lines);
    }

    private function buildDeterministicReply(Matchs $match, string $question): ?string
    {
        $q = $this->normalize($question);
        $home = trim((string)($match->getEquipeDomicile()?->getNom() ?? 'Equipe domicile'));
        $away = trim((string)($match->getEquipeExterieur()?->getNom() ?? 'Equipe exterieur'));

        if (str_contains($q, 'score') || str_contains($q, 'resultat')) {
            return sprintf(
                'Le score du match est %s - %s.',
                (string)($match->getScoreEquipeDomicile() ?? 0),
                (string)($match->getScoreEquipeExterieur() ?? 0)
            );
        }

        if (str_contains($q, 'date') || str_contains($q, 'heure')) {
            $date = $match->getDateMatch()?->format('d/m/Y') ?? 'N/A';
            $time = $match->getHeureDebut()?->format('H:i') ?? 'N/A';

            return "Le match {$home} vs {$away} a lieu le {$date} a {$time}.";
        }

        if (str_contains($q, 'lieu') || str_contains($q, 'stade')) {
            $location = trim((string)($match->getLieu() ?? 'N/A'));
            return "Le match se joue a {$location}.";
        }

        if (str_contains($q, 'type')) {
            return 'Type du match: ' . (string)($match->getType() ?? 'N/A') . '.';
        }

        if (str_contains($q, 'statut')) {
            return 'Statut du match: ' . (string)($match->getStatut() ?? 'N/A') . '.';
        }

        if (str_contains($q, 'joueur')
            || str_contains($q, 'joueurs')
            || str_contains($q, 'effectif')
            || str_contains($q, 'composition')
            || str_contains($q, 'liste')
            || str_contains($q, 'titulaire')
            || str_contains($q, 'remplacant')
            || str_contains($q, 'jouers')
        ) {
            $homePlayers = [];
            $awayPlayers = [];

            foreach ($match->getMatchLineups() as $lineup) {
                $player = $lineup->getJoueur();
                if ($player === null) {
                    continue;
                }

                $entry = [
                    'numero' => (int)($player->getNumero() ?? 0),
                    'label' => sprintf(
                        '#%s %s %s',
                        (string)($player->getNumero() ?? '?'),
                        trim((string)$player->getPrenom()),
                        trim((string)$player->getNom())
                    ),
                ];

                if ($lineup->getType() === 'domicile') {
                    $homePlayers[] = $entry;
                } elseif ($lineup->getType() === 'exterieur') {
                    $awayPlayers[] = $entry;
                }
            }

            usort($homePlayers, static fn (array $a, array $b): int => $a['numero'] <=> $b['numero']);
            usort($awayPlayers, static fn (array $a, array $b): int => $a['numero'] <=> $b['numero']);

            $homeList = !empty($homePlayers)
                ? implode(', ', array_map(static fn (array $p): string => $p['label'], $homePlayers))
                : 'Aucune information';
            $awayList = !empty($awayPlayers)
                ? implode(', ', array_map(static fn (array $p): string => $p['label'], $awayPlayers))
                : 'Aucune information';

            return "Joueurs {$home}: {$homeList}.\nJoueurs {$away}: {$awayList}.";
        }

        return null;
    }

    private function hasConfiguredApiKey(): bool
    {
        $key = trim($this->groqApiKey);
        if ($key === '') {
            return false;
        }

        return $key !== 'your_groq_api_key_here';
    }

    private function normalize(string $value): string
    {
        $normalized = mb_strtolower(trim($value));
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? '';

        return $normalized;
    }
}
