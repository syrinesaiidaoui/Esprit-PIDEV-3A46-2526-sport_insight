<?php

namespace App\Repository;

use App\Entity\ProductOrder\Order;
use App\Entity\ProductOrder\OrderItem;
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
     * @param string[] $names
     *
     * @return Product[]
     */
    public function findByNames(array $names): array
    {
        $normalizedNames = array_values(array_unique(array_filter(array_map(
            static fn (mixed $name): string => trim((string) $name),
            $names
        ))));

        if ($normalizedNames === []) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.name IN (:names)')
            ->setParameter('names', $normalizedNames)
            ->getQuery()
            ->getResult();
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
        $days = max(1, $days);
        $limit = max(1, $limit);
        $topProductIds = $this->getTopTrendingProductIds($days, $limit);
        if ($topProductIds === []) {
            return [];
        }

        $totalsByProductId = $this->computeTrendingTotalsByProductId($days);

        $products = $this->createQueryBuilder('p')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $topProductIds)
            ->getQuery()
            ->getResult();

        $productsById = [];
        foreach ($products as $product) {
            $productId = $product->getId();
            if ($productId !== null) {
                $productsById[$productId] = $product;
            }
        }

        $result = [];
        foreach ($topProductIds as $productId) {
            if (!isset($productsById[$productId])) {
                continue;
            }

            $totalSold = (int) ($totalsByProductId[$productId] ?? 0);
            if ($totalSold <= 0) {
                continue;
            }

            $result[] = [
                'product' => $productsById[$productId],
                'totalSold' => $totalSold,
            ];
        }

        return $result;
    }

    /**
     * Return only product names for the trending banner to reduce hydration overhead.
     *
     * @return string[]
     */
    public function findTrendingProductNames(int $days = 30, int $limit = 5): array
    {
        $days = max(1, $days);
        $limit = max(1, $limit);
        $topProductIds = $this->getTopTrendingProductIds($days, $limit);
        if ($topProductIds === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('p')
            ->select('p.id AS id', 'p.name AS name')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $topProductIds)
            ->getQuery()
            ->getArrayResult();

        $namesById = [];
        foreach ($rows as $row) {
            $productId = (int) ($row['id'] ?? 0);
            $name = trim((string) ($row['name'] ?? ''));
            if ($productId > 0 && $name !== '') {
                $namesById[$productId] = $name;
            }
        }

        $orderedNames = [];
        foreach ($topProductIds as $productId) {
            if (isset($namesById[$productId])) {
                $orderedNames[] = $namesById[$productId];
            }
        }

        return $orderedNames;
    }

    /**
     * @return array<int,int>
     */
    private function computeTrendingTotalsByProductId(int $days): array
    {
        $from = new \DateTimeImmutable(sprintf('-%d days', max(1, $days)));
        $statuses = ['confirmed', 'shipped', 'delivered'];
        $totalsByProductId = [];

        $legacyRows = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(o.product) AS productId')
            ->addSelect('SUM(o.quantity) AS totalSold')
            ->from(Order::class, 'o')
            ->leftJoin('o.items', 'legacyItems')
            ->andWhere('o.product IS NOT NULL')
            ->andWhere('legacyItems.id IS NULL')
            ->andWhere('o.quantity IS NOT NULL')
            ->andWhere('o.status IN (:statuses)')
            ->andWhere('o.orderDate >= :from')
            ->groupBy('o.product')
            ->setParameter('from', $from)
            ->setParameter('statuses', $statuses)
            ->getQuery()
            ->getArrayResult();

        foreach ($legacyRows as $row) {
            $productId = (int) ($row['productId'] ?? 0);
            if ($productId <= 0) {
                continue;
            }

            $totalsByProductId[$productId] = ($totalsByProductId[$productId] ?? 0) + (int) ($row['totalSold'] ?? 0);
        }

        $lineRows = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(oi.product) AS productId')
            ->addSelect('SUM(oi.quantity) AS totalSold')
            ->from(OrderItem::class, 'oi')
            ->innerJoin('oi.orderRef', 'ord')
            ->andWhere('ord.status IN (:statuses)')
            ->andWhere('ord.orderDate >= :from')
            ->groupBy('oi.product')
            ->setParameter('from', $from)
            ->setParameter('statuses', $statuses)
            ->getQuery()
            ->getArrayResult();

        foreach ($lineRows as $row) {
            $productId = (int) ($row['productId'] ?? 0);
            if ($productId <= 0) {
                continue;
            }

            $totalsByProductId[$productId] = ($totalsByProductId[$productId] ?? 0) + (int) ($row['totalSold'] ?? 0);
        }

        return $totalsByProductId;
    }

    /**
     * @return int[]
     */
    private function getTopTrendingProductIds(int $days, int $limit): array
    {
        $totalsByProductId = $this->computeTrendingTotalsByProductId($days);
        if ($totalsByProductId === []) {
            return [];
        }

        arsort($totalsByProductId, SORT_NUMERIC);

        return array_slice(array_keys($totalsByProductId), 0, max(1, $limit));
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
