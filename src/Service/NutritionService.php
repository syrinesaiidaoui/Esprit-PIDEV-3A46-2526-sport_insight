<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NutritionService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        // Using the API Ninjas key
        $this->apiKey = 'H1lpa2vrpr1et07YB7738cCBzCWYEXYNaF0DLzEy';
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
        if (str_contains($type, 'vel') || str_contains($type, 'cycl')) return 'cyclisme';
        if (str_contains($type, 'box')) return 'boxe';
        if (str_contains($type, 'yoga') || str_contains($type, 'pilat')) return 'yoga';
        
        return $type;
    }

    /**
     * Generate complete nutrition advice based on athlete performance scores
     */
    public function generateNutritionAdvice(float $physique, float $technique, float $tactique, string $typeEntrainement): array
    {
        $moyenne = round(($physique + $technique + $tactique) / 3, 1);
        $intensity = $this->getTrainingIntensity($moyenne);

        return [
            'macros' => $this->calculateMacros($physique, $technique, $tactique, $typeEntrainement, $intensity),
            'hydration' => $this->getHydrationAdvice($intensity, $typeEntrainement),
            'meals' => $this->getMealPlan($physique, $technique, $tactique, $typeEntrainement, $intensity),
            'pre_workout' => $this->getPreWorkout($typeEntrainement, $intensity),
            'post_workout' => $this->getPostWorkout($typeEntrainement, $intensity),
            'supplements' => $this->getSupplements($physique, $typeEntrainement),
            'nutrition_tip' => $this->getNutritionTip($moyenne, $typeEntrainement),
            'calories_estimate' => $this->estimateCalories($intensity, $typeEntrainement),
            'api_foods' => $this->fetchFoodNutrition($typeEntrainement),
        ];
    }

    private function getTrainingIntensity(float $moyenne): string
    {
        if ($moyenne < 8) return 'light';
        if ($moyenne < 14) return 'moderate';
        return 'intense';
    }

    private function calculateMacros(float $p, float $t, float $ta, string $type, string $intensity): array
    {
        // Base macros adjusted by sport and intensity
        $sportProfiles = [
            'football' => ['protein' => 30, 'carbs' => 50, 'fat' => 20],
            'basketball' => ['protein' => 30, 'carbs' => 50, 'fat' => 20],
            'tennis' => ['protein' => 25, 'carbs' => 55, 'fat' => 20],
            'natation' => ['protein' => 28, 'carbs' => 52, 'fat' => 20],
            'musculation' => ['protein' => 40, 'carbs' => 35, 'fat' => 25],
            'athletisme' => ['protein' => 25, 'carbs' => 55, 'fat' => 20],
            'cyclisme' => ['protein' => 20, 'carbs' => 65, 'fat' => 15],
            'boxe' => ['protein' => 35, 'carbs' => 45, 'fat' => 20],
            'yoga' => ['protein' => 20, 'carbs' => 50, 'fat' => 30],
        ];

        $matchedType = $this->matchSport($type);
        $profile = $sportProfiles[$matchedType] ?? ['protein' => 30, 'carbs' => 45, 'fat' => 25];

        // Adjust for physical score
        if ($p < 10) {
            $profile['protein'] += 5;
            $profile['carbs'] -= 5;
        }

        // Adjust for intensity
        if ($intensity === 'intense') {
            $profile['carbs'] += 5;
            $profile['fat'] -= 5;
        }

        return [
            'protein' => ['percent' => $profile['protein'], 'color' => '#ef4444', 'icon' => '🥩', 'label' => 'Protéines', 'description' => 'Réparation et croissance musculaire'],
            'carbs' => ['percent' => $profile['carbs'], 'color' => '#f59e0b', 'icon' => '🌾', 'label' => 'Glucides', 'description' => 'Énergie principale pour l\'effort'],
            'fat' => ['percent' => $profile['fat'], 'color' => '#22c55e', 'icon' => '🥑', 'label' => 'Lipides', 'description' => 'Hormones et absorption vitamines'],
        ];
    }

    private function estimateCalories(string $intensity, string $type): array
    {
        $baseCalories = [
            'light' => ['min' => 1800, 'max' => 2200],
            'moderate' => ['min' => 2200, 'max' => 2800],
            'intense' => ['min' => 2800, 'max' => 3500],
        ];

        $cal = $baseCalories[$intensity];

        $type = strtolower(trim($type));
        if (in_array($type, ['musculation', 'athletisme'])) {
            $cal['min'] += 300;
            $cal['max'] += 300;
        }

        return [
            'min' => $cal['min'],
            'max' => $cal['max'],
            'intensity' => $intensity,
        ];
    }

    private function getHydrationAdvice(string $intensity, string $type): array
    {
        $litres = match($intensity) {
            'light' => ['min' => 1.5, 'max' => 2.0],
            'moderate' => ['min' => 2.0, 'max' => 3.0],
            'intense' => ['min' => 3.0, 'max' => 4.0],
        };

        $type = strtolower(trim($type));
        if (in_array($type, ['natation', 'athletisme', 'tennis'])) {
            $litres['min'] += 0.5;
            $litres['max'] += 0.5;
        }

        return [
            'litres_min' => $litres['min'],
            'litres_max' => $litres['max'],
            'tips' => [
                '💧 Buvez 500ml 2h avant l\'entraînement',
                '💧 150-200ml toutes les 15 min pendant l\'effort',
                '💧 Replacez chaque kg perdu par 1.5L d\'eau après l\'effort',
            ],
            'electrolytes' => $intensity === 'intense',
        ];
    }

    private function getMealPlan(float $p, float $t, float $ta, string $type, string $intensity): array
    {
        $meals = [
            [
                'name' => 'Petit-déjeuner Énergie',
                'time' => '07:00 - 08:00',
                'icon' => '🌅',
                'image' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=400&auto=format&fit=crop&q=70',
                'foods' => $this->getBreakfastFoods($intensity, $type),
                'calories' => $intensity === 'intense' ? '550-700 kcal' : '400-550 kcal',
            ],
            [
                'name' => 'Déjeuner Performance',
                'time' => '12:00 - 13:00',
                'icon' => '☀️',
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=70',
                'foods' => $this->getLunchFoods($intensity, $type),
                'calories' => $intensity === 'intense' ? '700-900 kcal' : '550-700 kcal',
            ],
            [
                'name' => 'Collation Pré-entraînement',
                'time' => '15:30 - 16:00',
                'icon' => '⚡',
                'image' => 'https://images.unsplash.com/photo-1490474418585-ba9bad8fd0ea?w=400&auto=format&fit=crop&q=70',
                'foods' => ['Banane', 'Barre de céréales', 'Poignée d\'amandes', 'Yaourt grec'],
                'calories' => '200-300 kcal',
            ],
            [
                'name' => 'Dîner Récupération',
                'time' => '19:30 - 20:30',
                'icon' => '🌙',
                'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=400&auto=format&fit=crop&q=70',
                'foods' => $this->getDinnerFoods($intensity, $type),
                'calories' => $intensity === 'intense' ? '600-800 kcal' : '450-600 kcal',
            ],
        ];

        return $meals;
    }

    private function getBreakfastFoods(string $intensity, string $type): array
    {
        if ($intensity === 'intense') {
            return ['Flocons d\'avoine + miel + banane', 'Oeufs brouillés (3)', 'Pain complet + beurre de cacahuète', 'Jus d\'orange frais'];
        }
        if ($intensity === 'moderate') {
            return ['Yaourt grec + granola + fruits rouges', 'Toast complet + avocat', 'Smoothie protéiné'];
        }
        return ['Tartines complètes + confiture', 'Fruit frais', 'Thé vert ou café'];
    }

    private function getLunchFoods(string $intensity, string $type): array
    {
        $type = strtolower(trim($type));
        if (in_array($type, ['musculation', 'football', 'basketball'])) {
            return ['Poulet grillé / Saumon (200g)', 'Riz complet ou patate douce', 'Légumes verts variés', 'Huile d\'olive + citron'];
        }
        if (in_array($type, ['natation', 'athletisme', 'tennis'])) {
            return ['Pâtes complètes + sauce tomate', 'Thon / Dinde grillée', 'Salade composée', 'Pain complet'];
        }
        return ['Protéine au choix (150g)', 'Féculent complet', 'Légumes de saison', 'Fruit en dessert'];
    }

    private function getDinnerFoods(string $intensity, string $type): array
    {
        if ($intensity === 'intense') {
            return ['Poisson grillé / Omelette (3 oeufs)', 'Quinoa ou boulgour', 'Salade verte + tomates', 'Fromage blanc + miel'];
        }
        return ['Soupe de légumes', 'Protéine légère (oeufs, poisson)', 'Riz ou semoule', 'Fruit ou compote'];
    }

    private function getPreWorkout(string $type, string $intensity): array
    {
        return [
            'timing' => '1h30 - 2h avant',
            'image' => 'https://images.unsplash.com/photo-1622484211148-971fab4a55a0?w=400&auto=format&fit=crop&q=70',
            'foods' => [
                ['name' => 'Banane', 'benefit' => 'Énergie rapide + potassium', 'emoji' => '🍌'],
                ['name' => 'Flocons d\'avoine', 'benefit' => 'Énergie progressive', 'emoji' => '🥣'],
                ['name' => 'Beurre de cacahuète', 'benefit' => 'Lipides sains + satiété', 'emoji' => '🥜'],
                ['name' => 'Pain complet + miel', 'benefit' => 'Glucides complexes + simples', 'emoji' => '🍯'],
            ],
            'avoid' => ['Aliments gras / frits', 'Fibres excessives', 'Produits laitiers lourds'],
        ];
    }

    private function getPostWorkout(string $type, string $intensity): array
    {
        return [
            'timing' => 'Dans les 30 min après l\'effort',
            'image' => 'https://images.unsplash.com/photo-1502741224143-90386d7f8c82?w=400&auto=format&fit=crop&q=70',
            'foods' => [
                ['name' => 'Shake protéiné', 'benefit' => 'Récupération musculaire rapide', 'emoji' => '🥤'],
                ['name' => 'Poulet + riz', 'benefit' => 'Protéines + glucides complets', 'emoji' => '🍗'],
                ['name' => 'Yaourt + fruits', 'benefit' => 'Protéines + antioxydants', 'emoji' => '🍓'],
                ['name' => 'Oeufs + patate douce', 'benefit' => 'Construction musculaire', 'emoji' => '🥚'],
            ],
            'window' => $intensity === 'intense' ? 'Fenêtre anabolique : 30 min post-effort (crucial !!)' : 'Mangez dans l\'heure suivant l\'entraînement',
        ];
    }

    private function getSupplements(float $physique, string $type): array
    {
        $supplements = [
            ['name' => 'Whey Protéine', 'benefit' => 'Récupération et construction musculaire', 'when' => 'Post-entraînement', 'emoji' => '💪', 'recommended' => true],
            ['name' => 'Créatine', 'benefit' => 'Force et performance explosive', 'when' => 'Quotidien (5g)', 'emoji' => '⚡', 'recommended' => in_array(strtolower(trim($type)), ['musculation', 'football', 'basketball'])],
            ['name' => 'BCAA', 'benefit' => 'Réduction fatigue musculaire', 'when' => 'Pendant l\'effort', 'emoji' => '🔬', 'recommended' => $physique < 12],
            ['name' => 'Omega-3', 'benefit' => 'Anti-inflammatoire, santé articulaire', 'when' => 'Avec le repas', 'emoji' => '🐟', 'recommended' => true],
            ['name' => 'Vitamine D', 'benefit' => 'Os solides, immunité, énergie', 'when' => 'Le matin', 'emoji' => '☀️', 'recommended' => true],
            ['name' => 'Magnésium', 'benefit' => 'Réduction crampes, meilleur sommeil', 'when' => 'Le soir', 'emoji' => '💎', 'recommended' => $physique < 10],
        ];

        return $supplements;
    }

    private function getNutritionTip(float $moyenne, string $type): string
    {
        $tips = [
            'La nutrition représente 70% de vos résultats sportifs. Un bon plan alimentaire est aussi important que l\'entraînement.',
            'Évitez les sucres raffinés avant l\'effort. Privilégiez les glucides complexes pour une énergie stable.',
            'Les protéines doivent être réparties sur tous les repas (20-30g par repas) pour une absorption optimale.',
            'Ne négligez pas les lipides : ils sont essentiels pour la production d\'hormones et l\'absorption des vitamines.',
            'L\'hydratation est souvent sous-estimée. Une déshydratation de 2% réduit les performances de 20%.',
            'Mangez des couleurs variées : chaque couleur de légume apporte des micronutriments différents.',
            'Le sommeil est le meilleur complément alimentaire. 7-9h par nuit optimisent la récupération.',
        ];

        return $tips[array_rand($tips)];
    }

    /**
     * Search specific food nutrition
     */
    public function findNutrition(string $query): ?array
    {
        if (empty($this->apiKey) || empty($query)) {
            return [];
        }

        // Basic translation for French users
        $translations = [
            'poulet' => 'chicken',
            'oeuf' => 'egg',
            'oeufs' => 'eggs',
            'viande' => 'meat',
            'poisson' => 'fish',
            'riz' => 'rice',
            'pates' => 'pasta',
            'pomme' => 'apple',
            'banane' => 'banana',
            'lait' => 'milk',
            'pain' => 'bread',
            'beurre' => 'butter',
            'fromage' => 'cheese',
        ];

        $searchQuery = strtolower(trim($query));
        foreach ($translations as $fr => $en) {
            $searchQuery = str_replace($fr, $en, $searchQuery);
        }

        try {
            $response = $this->client->request('GET', 'https://api.api-ninjas.com/v1/nutrition', [
                'headers' => ['X-Api-Key' => $this->apiKey],
                'query' => ['query' => $searchQuery],
                'timeout' => 8,
            ]);

            if ($response->getStatusCode() === 200) {
                // API Ninjas v1/nutrition returns a flat JSON array directly
                return $response->toArray();
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Try to fetch real nutrition data from API Ninjas (CalorieNinjas)
     */
    private function fetchFoodNutrition(string $type): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $sportFoods = [
            'football' => '200g chicken breast, 150g brown rice, broccoli',
            'basketball' => '200g turkey breast, 200g whole wheat pasta',
            'tennis' => '150g salmon fillet, 100g quinoa, asparagus',
            'natation' => 'bowl of oatmeal, 1 banana, honey, 2 eggs',
            'musculation' => '250g grilled beef, 200g sweet potato, spinach',
            'athletisme' => '150g chicken breast, 200g pasta, 1 apple',
            'cyclisme' => '300g pasta with tomato sauce, 1 banana',
            'boxe' => '200g lean beef, 1 cup of brown rice, salad',
            'yoga' => 'avocado toast with 2 poached eggs, green smoothie',
        ];

        $matchedType = $this->matchSport($type);
        // If it matched a known sport, use the predefined query. 
        // Otherwise, assume the user might have entered a food query directly.
        $query = $sportFoods[$matchedType] ?? $type;
        
        if (empty($query)) {
            $query = '200g chicken breast and 150g rice';
        }

        return $this->findNutrition($query);
    }
}
