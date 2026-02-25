<?php

namespace App\Controller\BackOffice;

use App\Service\AdminDashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard', methods: ['GET'])]
    public function index(AdminDashboardService $dashboardService, ChartBuilderInterface $chartBuilder): Response
    {
        $data = $dashboardService->buildDashboardData();

        $revenueChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $revenueChart->setData([
            'labels' => array_map(static fn (array $month): string => (string) $month['label'], $data['monthlyStats']),
            'datasets' => [[
                'label' => 'Monthly Revenue ($)',
                'data' => array_map(static fn (array $month): float => (float) $month['revenue'], $data['monthlyStats']),
                'borderColor' => 'rgb(16, 185, 129)',
                'backgroundColor' => 'rgba(16, 185, 129, 0.18)',
                'fill' => true,
                'tension' => 0.35,
            ]],
        ]);
        $revenueChart->setOptions([
            'plugins' => ['legend' => ['display' => true]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ]);

        $topProductsChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $topProductsChart->setData([
            'labels' => array_map(static fn (array $product): string => (string) $product['name'], $data['topSellingProducts']),
            'datasets' => [[
                'label' => 'Units Sold',
                'data' => array_map(static fn (array $product): int => (int) $product['quantity'], $data['topSellingProducts']),
                'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                'borderColor' => 'rgb(29, 78, 216)',
                'borderWidth' => 1,
            ]],
        ]);
        $topProductsChart->setOptions([
            'plugins' => ['legend' => ['display' => true]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ]);

        return $this->render('back_office/dashboard/index.html.twig', array_merge($data, [
            'revenueChart' => $revenueChart,
            'topProductsChart' => $topProductsChart,
        ]));
    }
}
