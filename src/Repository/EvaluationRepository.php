<?php

namespace App\Repository;

use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    /**
     * Calcule la moyenne des compétences de chaque joueur pour l'entraîneur connecté.
     */
    public function getFootballStatsByUser($coach): array
    {
        return $this->createQueryBuilder('e')
            ->select(
                'j.id as userId',
                'j.nom as userNom',
                'j.prenom as userPrenom',
                'j.photo as userPhoto',
                'AVG(e.notePhysique) as avgPhysique',
                'AVG(e.noteTechnique) as avgTechnique',
                'AVG(e.noteTactique) as avgTactique',
                'COUNT(e.id) as evalsCount'
            )
            ->join('e.joueur', 'j')
            ->join('e.entrainement', 't')
            ->andWhere('t.entraineur = :coach')
            ->setParameter('coach', $coach)
            ->groupBy('j.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère l'évolution chronologique des compétences moyennes de l'équipe de l'entraîneur
     */
    public function getTeamEvolutionOverTime($coach): array
    {
        return $this->createQueryBuilder('e')
            ->select(
                't.dateEntrainement as date',
                'AVG(e.notePhysique) as avgPhysique',
                'AVG(e.noteTechnique) as avgTechnique',
                'AVG(e.noteTactique) as avgTactique'
            )
            ->join('e.entrainement', 't')
            ->andWhere('t.entraineur = :coach')
            ->setParameter('coach', $coach)
            ->groupBy('t.dateEntrainement')
            ->orderBy('t.dateEntrainement', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
