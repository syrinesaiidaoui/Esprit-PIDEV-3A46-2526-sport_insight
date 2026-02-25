<?php

namespace App\Service;

use App\Repository\EvaluationRepository;

class MatchmakingService
{
    private $evaluationRepository;

    public function __construct(EvaluationRepository $evaluationRepository)
    {
        $this->evaluationRepository = $evaluationRepository;
    }

    /**
     * Calcule la moyenne des compétences de Football en utilisant les relations de la BD.
     * @return array
     */
    public function generateMatchmakingGroups($coach): array
    {
        $stats = $this->evaluationRepository->getFootballStatsByUser($coach);
        
        $players = [];

        foreach ($stats as $row) {
            $avgPhysique = round((float)$row['avgPhysique'], 2);
            $avgTechnique = round((float)$row['avgTechnique'], 2);
            $avgTactique = round((float)$row['avgTactique'], 2);
            
            $globalAverage = round(($avgPhysique + $avgTechnique + $avgTactique) / 3, 2);
            
            // Logique de groupes: Équipes Homogènes (Football)
            $group = 'Non classé';
            $badgeColor = '#94a3b8';
            if ($globalAverage >= 16) {
                $group = 'Équipe Elite (A)';
                $badgeColor = '#8b5cf6';
            } elseif ($globalAverage >= 13) {
                $group = 'Équipe Avancée (B)';
                $badgeColor = '#22c55e';
            } elseif ($globalAverage >= 10) {
                $group = 'Équipe Intermédiaire (C)';
                $badgeColor = '#f59e0b';
            } else {
                $group = 'Équipe Développement (D)';
                $badgeColor = '#ef4444';
            }

            $players[] = [
                'userNom' => ucwords(strtolower(trim($row['userNom']))),
                'userPrenom' => ucwords(strtolower(trim($row['userPrenom']))),
                'userPhoto' => $row['userPhoto'],
                'userId' => $row['userId'],
                'avgPhysique' => $avgPhysique,
                'avgTechnique' => $avgTechnique,
                'avgTactique' => $avgTactique,
                'globalAverage' => $globalAverage,
                'group' => $group,
                'badgeColor' => $badgeColor,
            ];
        }

        // Trier les joueurs du sport par score global décroissant
        usort($players, function($a, $b) {
            return $b['globalAverage'] <=> $a['globalAverage'];
        });

        // Organiser par Équipes (Groupes)
        $groups = [];
        foreach ($players as $player) {
            $groupName = $player['group'];
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [
                    'name' => $groupName,
                    'color' => $player['badgeColor'],
                    'players' => []
                ];
            }
            $groups[$groupName]['players'][] = $player;
        }

        return $groups;
    }
}
