<?php

namespace App\Repository;

use App\Entity\ContratSponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContratSponsor>
 */
class ContratSponsorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContratSponsor::class);
    }

    /**
     * Recherche les contrats par nom de sponsor et/ou date début
     */
    public function searchContrats(?string $sponsorNom = null, ?\DateTime $dateDebut = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('s')
            ->addSelect('e')
            ->innerJoin('c.sponsor', 's')
            ->innerJoin('c.equipe', 'e')
            ->orderBy('c.dateDebut', 'DESC');

        if ($sponsorNom) {
            $qb->andWhere('s.nom LIKE :sponsorNom')
                ->setParameter('sponsorNom', '%' . $sponsorNom . '%');
        }

        if ($dateDebut) {
            $qb->andWhere('c.dateDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all contracts expiring on or before the given date
     *
     * @param \DateTime $date The date to check for expiration
     * @return ContratSponsor[] Array of expired contracts
     */
    public function findByExpirationDate(\DateTime $date): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('s', 'e')
            ->innerJoin('c.sponsor', 's')
            ->innerJoin('c.equipe', 'e')
            ->where('c.dateFin <= :date')
            ->andWhere('c.statut != :expired')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('expired', 'Expiré')
            ->orderBy('c.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find contracts expiring within N days (for upcoming expiration alerts)
     *
     * @param int $days Number of days ahead to check
     * @return ContratSponsor[] Array of contracts soon to expire
     */
    public function findExpiringWithinDays(int $days = 7): array
    {
        $today = new \DateTime();
        $futureDate = (clone $today)->modify("+{$days} days");

        return $this->createQueryBuilder('c')
            ->addSelect('s', 'e')
            ->innerJoin('c.sponsor', 's')
            ->innerJoin('c.equipe', 'e')
            ->where('c.dateFin > :today')
            ->andWhere('c.dateFin <= :future')
            ->andWhere('c.statut != :expired')
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('future', $futureDate->format('Y-m-d'))
            ->setParameter('expired', 'Expiré')
            ->orderBy('c.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ContratSponsor
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
