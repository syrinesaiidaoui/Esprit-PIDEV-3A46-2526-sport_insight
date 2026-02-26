<?php

namespace App\Repository;

use App\Entity\ProductOrder\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Return an ordered list of distinct non-null categories.
     * @return string[]
     */
    public function findDistinctCategories(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT p.category as category')
            ->andWhere('p.category IS NOT NULL')
            ->orderBy('p.category', 'ASC');

        $rows = $qb->getQuery()->getScalarResult();

        return array_map(fn($r) => $r['category'], $rows);
    }

    /**
     * Search products by optional query, category and sort mode.
     * @param string|null $q
     * @param string|null $category
     * @param string|null $sort "name"|"price"|"stock"
     * @return Product[]
     */
    public function searchProducts(?string $q = null, ?string $category = null, ?string $sort = null): array
    {
        return $this->createSearchQueryBuilder($q, $category, $sort)->getQuery()->getResult();
    }

    public function createSearchQueryBuilder(?string $q = null, ?string $category = null, ?string $sort = null, string $direction = 'ASC'): QueryBuilder
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $qb = $this->createQueryBuilder('p');

        if ($q) {
            $qb->andWhere('LOWER(p.name) LIKE :q OR LOWER(p.category) LIKE :q OR LOWER(p.brand) LIKE :q')
                ->setParameter('q', '%' . strtolower($q) . '%');
        }

        if ($category) {
            $aliases = $this->expandCategoryAliases(strtolower(trim($category)));
            $orX = $qb->expr()->orX();

            foreach ($aliases as $index => $alias) {
                $parameter = 'cat_' . $index;
                $orX->add('LOWER(p.category) LIKE :' . $parameter);
                $qb->setParameter($parameter, '%' . $alias . '%');
            }

            if (count($orX->getParts()) > 0) {
                $qb->andWhere($orX);
            }
        }

        switch ($sort) {
            case 'price':
                $qb->orderBy('p.price', $direction);
                break;
            case 'stock':
                $qb->orderBy('p.stock', $direction === 'ASC' ? 'ASC' : 'DESC');
                break;
            default:
                $qb->orderBy('p.name', $direction);
        }

        return $qb;
    }

    /**
     * Expand user-facing category names (e.g. Shoes, Pulls) to DB-compatible aliases.
     * @return string[]
     */
    private function expandCategoryAliases(string $category): array
    {
        $normalized = strtolower(trim($category));
        if ($normalized === '') {
            return [];
        }

        $groups = [
            ['boots', 'boot', 'shoes', 'shoe', 'cleats', 'chaussure', 'chaussures'],
            ['jersey', 'jerseys', 'maillot', 'maillots', 'shirt', 'shirts'],
            ['pull', 'pulls', 'hoodie', 'hoodies', 'sweat', 'sweats', 'training wear', 'training'],
            ['ball', 'balls', 'ballon', 'ballons'],
            ['glove', 'gloves', 'gant', 'gants'],
            ['accessory', 'accessories', 'accessoire', 'accessoires'],
            ['protection', 'protections'],
        ];

        $aliases = [$normalized];
        foreach ($groups as $group) {
            if (in_array($normalized, $group, true)) {
                $aliases = array_merge($aliases, $group);
            }
        }

        return array_values(array_unique(array_filter(array_map('trim', $aliases))));
    }

    /**
     * Find trending products by summing quantities in orders within the last $days days.
     * Returns an array of ['product' => Product, 'totalSold' => int]
     */
    public function findTrending(int $days = 30, int $limit = 5): array
    {
        $from = new \DateTime(sprintf('-%d days', $days));
        $statuses = ['confirmed', 'shipped', 'delivered'];

        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->addSelect('(COALESCE(SUM(o.quantity), 0) + COALESCE(SUM(oi.quantity), 0)) AS totalSold')
            ->leftJoin('p.orders', 'o', 'WITH', 'o.status IN (:statuses) AND o.orderDate >= :from')
            ->leftJoin('p.orderItems', 'oi')
            ->leftJoin('oi.orderRef', 'ord', 'WITH', 'ord.status IN (:statuses) AND ord.orderDate >= :from')
            ->setParameter('from', $from)
            ->setParameter('statuses', $statuses)
            ->groupBy('p.id')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults($limit);

        $result = $qb->getQuery()->getResult();

        // Doctrine returns an array: [0 => Product, 'totalSold' => n]
        return array_map(static function (array $row): array {
            return [
                'product' => $row[0],
                'totalSold' => (int) $row['totalSold'],
            ];
        }, $result);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
