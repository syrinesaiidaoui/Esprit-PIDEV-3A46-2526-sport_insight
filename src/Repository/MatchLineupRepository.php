<?php

namespace App\Repository;

use App\Entity\MatchLineup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatchLineup>
 */
class MatchLineupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchLineup::class);
    }

    /**
     * Trouve all joueurs d'une équipe pour un match spécifique
     */
    public function findByMatchAndType(int $matchId, string $type)
    {
        return $this->createQueryBuilder('ml')
            ->andWhere('ml.matchs = :matchId')
            ->andWhere('ml.type = :type')
            ->setParameter('matchId', $matchId)
            ->setParameter('type', $type)
            ->orderBy('ml.joueur', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de joueurs pour une équipe dans un match
     */
    public function countByMatchAndType(int $matchId, string $type): int
    {
        return $this->createQueryBuilder('ml')
            ->select('COUNT(ml.id)')
            ->andWhere('ml.matchs = :matchId')
            ->andWhere('ml.type = :type')
            ->setParameter('matchId', $matchId)
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
