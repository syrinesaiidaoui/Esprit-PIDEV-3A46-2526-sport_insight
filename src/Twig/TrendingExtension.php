<?php

namespace App\Twig;

use App\Service\TrendingService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TrendingExtension extends AbstractExtension
{
    public function __construct(private TrendingService $trendingService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('trending_product_names', [$this, 'getTrendingProductNames']),
        ];
    }

    /**
     * @return string[]
     */
    public function getTrendingProductNames(int $days = 30, int $limit = 5): array
    {
        return $this->trendingService->getTrendingProductNames($days, $limit);
    }
}
