<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TrendingService
{
    private const CACHE_TTL_SECONDS = 120;

    public function __construct(
        private ProductRepository $productRepository,
        private CacheInterface $cache
    ) {}

    /**
     * Get trending products for the given period (days) and limit.
     * Returns an array of ['product' => Product, 'totalSold' => int]
     */
    public function getTrending(int $days = 30, int $limit = 5): array
    {
        $days = max(1, $days);
        $limit = max(1, $limit);

        return $this->productRepository->findTrending($days, $limit);
    }

    /**
     * Get trending product names with a short cache to avoid repeated heavy queries.
     *
     * @return string[]
     */
    public function getTrendingProductNames(int $days = 30, int $limit = 5): array
    {
        $days = max(1, $days);
        $limit = max(1, $limit);
        $cacheKey = sprintf('front.trending.names.v1.%d.%d', $days, $limit);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($days, $limit): array {
            $item->expiresAfter(self::CACHE_TTL_SECONDS);

            return $this->productRepository->findTrendingProductNames($days, $limit);
        });
    }
}
