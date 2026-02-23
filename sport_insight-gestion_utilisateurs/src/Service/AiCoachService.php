<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiCoachService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = 'AIzaSyBT31YcxJOY8PWZadw_oaNWXX-p8DF3958';
    }

    private function matchSport(string $type): string
    {
        $type = strtolower(trim($type));
        if (str_contains($type, 'foot')) return 'football';
        if (str_contains($type, 'basket')) return 'basketball';
        if (str_contains($type, 'tenn')) return 'tennis';
        if (str_contains($type, 'nata') || str_contains($type, 'swim')) return 'natation';
        if (str_contains($type, 'muscu') || str_contains($type, 'physique') || str_contains($type, 'body')) return 'musculation';
        if (str_contains($type, 'athle') || str_contains($type, 'course') || str_contains($type, 'run')) return 'athletisme';
        return 'sport';
    }

    /**
     * Returns a structured array with advice, exercises, and scores analysis
     */
    public function generateStructuredAdvice($physique, $technique, $tactique, $typeEntrainement): array
    {
        $p = (float)$physique;
        $t = (float)$technique;
        $ta = (float)$tactique;
        $moyenne = round(($p + $t + $ta) / 3, 1);

        // Try Gemini API first
        $apiAdvice = $this->tryGeminiApi($p, $t, $ta, $typeEntrainement);

        // Build structured response
        return [
            'scores' => [
                'physique' => $p,
                'technique' => $t,
                'tactique' => $ta,
                'moyenne' => $moyenne,
            ],
            'level' => $this->getLevel($moyenne),
            'physique_analysis' => $this->analyzePhysique($p),
            'technique_analysis' => $this->analyzeTechnique($t),
            'tactique_analysis' => $this->analyzeTactique($ta),
            'exercises' => $this->getExercises($p, $t, $ta, $typeEntrainement),
            'training_tip' => $this->getTrainingTip($typeEntrainement, $moyenne),
            'motivation' => $this->getMotivation($moyenne),
            'ai_advice' => $apiAdvice,
        ];
    }

    // Keep backward compatibility
    public function generateAdvice($physique, $technique, $tactique, $typeEntrainement): string
    {
        $result = $this->generateStructuredAdvice($physique, $technique, $tactique, $typeEntrainement);
        if ($result['ai_advice']) {
            return $result['ai_advice'];
        }
        return $this->formatAsText($result);
    }

    private function getLevel(float $moyenne): array
    {
        if ($moyenne < 8) return ['name' => 'Débutant', 'color' => '#ef4444', 'icon' => '🌱', 'progress' => round($moyenne / 20 * 100)];
        if ($moyenne < 12) return ['name' => 'Intermédiaire', 'color' => '#f59e0b', 'icon' => '⚡', 'progress' => round($moyenne / 20 * 100)];
        if ($moyenne < 16) return ['name' => 'Avancé', 'color' => '#22c55e', 'icon' => '🔥', 'progress' => round($moyenne / 20 * 100)];
        return ['name' => 'Expert', 'color' => '#8b5cf6', 'icon' => '🏆', 'progress' => round($moyenne / 20 * 100)];
    }

    private function analyzePhysique(float $p): array
    {
        if ($p < 8) return ['status' => 'faible', 'color' => '#ef4444', 'emoji' => '⚠️', 'message' => 'Priorisez le renforcement musculaire et le cardio. 3 séances/semaine minimum.'];
        if ($p < 12) return ['status' => 'moyen', 'color' => '#f59e0b', 'emoji' => '📊', 'message' => 'Ajoutez des exercices de résistance et d\'endurance. Intégrez du HIIT.'];
        if ($p < 16) return ['status' => 'bon', 'color' => '#22c55e', 'emoji' => '👍', 'message' => 'Maintenez avec des séances variées. Travaillez la pliométrie.'];
        return ['status' => 'excellent', 'color' => '#8b5cf6', 'emoji' => '🏆', 'message' => 'Niveau élite ! Focus récupération active et prévention blessures.'];
    }

    private function analyzeTechnique(float $t): array
    {
        if ($t < 8) return ['status' => 'à améliorer', 'color' => '#ef4444', 'emoji' => '⚠️', 'message' => 'Travaillez les fondamentaux avec des drills de répétition.'];
        if ($t < 12) return ['status' => 'correcte', 'color' => '#f59e0b', 'emoji' => '📊', 'message' => 'Affinez vos gestes avec des exercices spécifiques.'];
        if ($t < 16) return ['status' => 'bonne', 'color' => '#22c55e', 'emoji' => '👍', 'message' => 'Travaillez la précision sous pression et la vitesse d\'exécution.'];
        return ['status' => 'excellente', 'color' => '#8b5cf6', 'emoji' => '🏆', 'message' => 'Maîtrise remarquable ! Perfectionnez les gestes avancés.'];
    }

    private function analyzeTactique(float $ta): array
    {
        if ($ta < 8) return ['status' => 'faible', 'color' => '#ef4444', 'emoji' => '⚠️', 'message' => 'Étudiez les schémas de jeu. Visionnez des matchs analysés.'];
        if ($ta < 12) return ['status' => 'moyen', 'color' => '#f59e0b', 'emoji' => '📊', 'message' => 'Améliorez la prise de décision avec des jeux réduits (3v3).'];
        if ($ta < 16) return ['status' => 'bon', 'color' => '#22c55e', 'emoji' => '👍', 'message' => 'Développez votre anticipation. Analysez vos performances vidéo.'];
        return ['status' => 'excellent', 'color' => '#8b5cf6', 'emoji' => '🏆', 'message' => 'Vision tactique remarquable ! Guidez vos coéquipiers.'];
    }

    private function getExercises(float $p, float $t, float $ta, string $type): array
    {
        $exercises = [];

        // Exercises based on weakest areas
        if ($p <= $t && $p <= $ta) {
            // Physique is weakest
            $exercises[] = [
                'name' => 'Course fractionnée',
                'description' => '30/30 (30s sprint, 30s repos) × 10 répétitions',
                'duration' => '15 min',
                'intensity' => $p < 10 ? 'Modérée' : 'Haute',
                'image' => 'cardio',
                'category' => 'Cardio',
                'categoryColor' => '#ef4444',
            ];
            $exercises[] = [
                'name' => 'Gainage & Renforcement',
                'description' => 'Planche 45s + squats 15 reps + pompes 12 reps × 3 séries',
                'duration' => '20 min',
                'intensity' => 'Modérée',
                'image' => 'strength',
                'category' => 'Force',
                'categoryColor' => '#f59e0b',
            ];
        }

        if ($t <= $p && $t <= $ta) {
            // Technique is weakest
            $exercises[] = [
                'name' => 'Drills techniques',
                'description' => 'Répétitions de gestes fondamentaux avec focus sur la précision',
                'duration' => '20 min',
                'intensity' => 'Basse',
                'image' => 'technique',
                'category' => 'Technique',
                'categoryColor' => '#3b82f6',
            ];
            $exercises[] = [
                'name' => 'Exercices de coordination',
                'description' => 'Échelle de rythme + slalom + exercices de dissociation',
                'duration' => '15 min',
                'intensity' => 'Modérée',
                'image' => 'flexibility',
                'category' => 'Agilité',
                'categoryColor' => '#8b5cf6',
            ];
        }

        if ($ta <= $p && $ta <= $t) {
            // Tactique is weakest
            $exercises[] = [
                'name' => 'Jeux en situation réduite',
                'description' => 'Matchs 3v3 ou 4v4 avec contraintes tactiques',
                'duration' => '25 min',
                'intensity' => 'Haute',
                'image' => 'tactics',
                'category' => 'Tactique',
                'categoryColor' => '#14b8a6',
            ];
        }

        // Always add a HIIT/general exercise
        $exercises[] = [
            'name' => 'Circuit Training HIIT',
            'description' => 'Burpees + mountain climbers + jump squats + sprints (40s effort / 20s repos)',
            'duration' => '18 min',
            'intensity' => 'Haute',
            'image' => 'hiit',
            'category' => 'HIIT',
            'categoryColor' => '#ef4444',
        ];

        // Add sport-specific exercise
        $sportExercise = $this->getSportSpecificExercise($type, $p, $t, $ta);
        if ($sportExercise) {
            $exercises[] = $sportExercise;
        }

        return $exercises;
    }

    private function getSportSpecificExercise(string $type, float $p, float $t, float $ta): ?array
    {
        $type = strtolower(trim($type));

        $map = [
            'football' => [
                'name' => 'Jeu de passes & contrôle',
                'description' => 'Triangle de passes, contrôle orienté, conduite de balle en slalom',
                'duration' => '20 min',
                'intensity' => 'Modérée',
                'image' => 'technique',
                'category' => 'Football',
                'categoryColor' => '#22c55e',
            ],
            'basketball' => [
                'name' => 'Dribble & tir en mouvement',
                'description' => 'Parcours dribble + lay-ups main droite/gauche + tir mi-distance',
                'duration' => '20 min',
                'intensity' => 'Modérée',
                'image' => 'technique',
                'category' => 'Basketball',
                'categoryColor' => '#f97316',
            ],
            'tennis' => [
                'name' => 'Jeu de fond de court',
                'description' => 'Échanges croisés/décroisés + jeu de jambes + volées',
                'duration' => '25 min',
                'intensity' => 'Haute',
                'image' => 'cardio',
                'category' => 'Tennis',
                'categoryColor' => '#eab308',
            ],
            'natation' => [
                'name' => 'Séries techniques nage',
                'description' => '4×50m éducatifs + 4×100m allure modérée + 200m récupération',
                'duration' => '30 min',
                'intensity' => 'Modérée',
                'image' => 'cardio',
                'category' => 'Natation',
                'categoryColor' => '#06b6d4',
            ],
            'musculation' => [
                'name' => 'Programme Full Body',
                'description' => 'Squat + développé couché + rowing + soulevé de terre (4×8 reps)',
                'duration' => '45 min',
                'intensity' => 'Haute',
                'image' => 'strength',
                'category' => 'Musculation',
                'categoryColor' => '#6366f1',
            ],
            'athletisme' => [
                'name' => 'Entraînement piste',
                'description' => 'Gammes athlétiques + 6×200m à 80% + étirements dynamiques',
                'duration' => '35 min',
                'intensity' => 'Haute',
                'image' => 'cardio',
                'category' => 'Athlétisme',
                'categoryColor' => '#ec4899',
            ],
        ];

        return $map[$type] ?? [
            'name' => 'Entraînement spécifique',
            'description' => 'Exercices adaptés à votre discipline avec focus technique',
            'duration' => '25 min',
            'intensity' => 'Modérée',
            'image' => 'technique',
            'category' => ucfirst($type ?: 'Sport'),
            'categoryColor' => '#16a34a',
        ];
    }

    private function getTrainingTip(string $type, float $moyenne): string
    {
        $sport = $this->matchSport($type);
        
        $tips = [
            'football' => "Travaillez votre explosivité sur les premiers mètres et votre vision de jeu.",
            'basketball' => "Focus sur la détente verticale et la précision au tir sous fatigue.",
            'tennis' => "Améliorez votre jeu de jambes et votre endurance de fond de court.",
            'natation' => "Concentrez-vous sur l'hydrodynamisme et la régularité de vos battements.",
            'musculation' => "Priorisez la forme d'exécution avant la charge. N'oubliez pas le cardio.",
            'athletisme' => "Le travail de foulée et la gestion du souffle sont vos priorités.",
            'sport' => "Visez la régularité : 3 séances par semaine est l'idéal pour progresser."
        ];

        $baseTip = $tips[$sport] ?? $tips['sport'];

        if ($moyenne < 10) {
            return $baseTip . " Commencez par des bases solides.";
        } elseif ($moyenne < 15) {
            return $baseTip . " Variez les intensités pour franchir un palier.";
        }
        return $baseTip . " Optimisez votre récupération pour maintenir ce niveau expert.";
    }

    private function getMotivation(float $moyenne): string
    {
        $quotes = [
            "« Le talent gagne des matchs, mais le travail d'équipe gagne des championnats. » — Michael Jordan",
            "« La douleur est temporaire, la fierté est éternelle. »",
            "« Chaque entraînement est une victoire sur soi-même. »",
            "« Le succès n'est pas définitif, l'échec n'est pas fatal. C'est le courage de continuer qui compte. » — W. Churchill",
            "« La seule façon de définir ses limites, c'est de les dépasser. » — Arthur C. Clarke",
            "« Tu ne trouves pas le temps pour t'entraîner, tu CRÉES le temps. »",
            "« Plus tu transpires à l'entraînement, moins tu saignes au combat. »",
        ];
        return $quotes[array_rand($quotes)];
    }

    private function tryGeminiApi(float $p, float $t, float $ta, string $typeEntrainement): ?string
    {
        $prompt = "Analyse ces notes de sport sur 20 : Physique $p, Technique $t, Tactique $ta. Entraînement : $typeEntrainement. Donne 2 conseils courts.";

        try {
            $response = $this->client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=' . $this->apiKey, [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ],
                'timeout' => 10,
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function formatAsText(array $result): string
    {
        $lines = ["🤖 **Analyse AI Coach :**\n"];
        $lines[] = "Physique ({$result['scores']['physique']}/20) : {$result['physique_analysis']['message']}";
        $lines[] = "Technique ({$result['scores']['technique']}/20) : {$result['technique_analysis']['message']}";
        $lines[] = "Tactique ({$result['scores']['tactique']}/20) : {$result['tactique_analysis']['message']}";
        $lines[] = "\n💡 " . $result['training_tip'];
        $lines[] = "\n🔥 " . $result['motivation'];
        return implode("\n", $lines);
    }
}