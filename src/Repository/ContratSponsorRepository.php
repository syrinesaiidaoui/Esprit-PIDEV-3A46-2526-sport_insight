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
     * Retourne les contrats qui expirent dans les $days prochains jours.
     *
     * @return ContratSponsor[]
     */
    public function findExpiringWithinDays(int $days): array
    {
        $now = new \DateTimeImmutable('today');
        $limit = $now->modify(sprintf('+%d days', $days));

        return $this->createQueryBuilder('c')
            ->andWhere('c.dateFin BETWEEN :now AND :limit')
            ->setParameter('now', $now)
            ->setParameter('limit', $limit)
            ->orderBy('c.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les contrats qui expirent avant ou à la date donnée.
     *
     * @return ContratSponsor[]
     */
    public function findByExpirationDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dateFin <= :date')
            ->setParameter('date', $date)
            ->orderBy('c.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche simple sur le sponsor, l'équipe ou le statut.
     *
     * @return ContratSponsor[]
     */
    public function searchContrats(?string $keyword = null, ?\DateTimeInterface $startDate = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.sponsor', 's')
            ->leftJoin('c.equipe', 'e')
            ->addSelect('s', 'e')
            ->orderBy('c.dateFin', 'DESC');

        $search = $keyword ? trim(mb_strtolower($keyword)) : null;
        if ($search !== null && $search !== '') {
            $qb->andWhere('LOWER(s.nom) LIKE :kw OR LOWER(e.nom) LIKE :kw OR LOWER(c.statut) LIKE :kw')
                ->setParameter('kw', '%' . $search . '%');
        }

        if ($startDate !== null) {
            $qb->andWhere('c.dateDebut >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return ContratSponsor[] Returns an array of ContratSponsor objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
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
