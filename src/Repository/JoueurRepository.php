<?php

namespace App\Repository;

use App\Entity\Joueur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Joueur>
 */
class JoueurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joueur::class);
    }

    /**
     * @return Joueur[] Returns an array of Joueur objects
     */
    public function findAll(): array
    {
        return $this->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']);
    }

    /**
     * @return Joueur[] Returns an array of Joueur objects by Equipe
     */
    public function findByEquipe($equipeId): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.equipe = :equipe_id')
            ->setParameter('equipe_id', $equipeId)
            ->orderBy('j.nom', 'ASC')
            ->addOrderBy('j.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
