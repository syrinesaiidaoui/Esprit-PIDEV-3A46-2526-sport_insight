<?php

namespace App\Controller;

use App\Repository\ContratSponsorRepository;
use App\Repository\SponsorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard')]
    public function index(
        ChartBuilderInterface $chartBuilder,
        ContratSponsorRepository $contratRepo,
        SponsorRepository $sponsorRepo
    ): Response
    {
        // Données pour la barre sponsors vs contrats
        $totalSponsors = count($sponsorRepo->findAll());
        $totalContrats = count($contratRepo->findAll());
        
        $chartSponsors = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartSponsors->setData([
            'labels' => ['Sponsors', 'Contrats'],
            'datasets' => [[
                'label' => 'Nombre total',
                'data' => [$totalSponsors, $totalContrats],
                'backgroundColor' => ['#10b981', '#3b82f6'],
                'borderColor' => ['#059669', '#2563eb'],
                'borderWidth' => 2,
            ]],
        ]);
        $chartSponsors->setOptions([
            'responsive' => true,
            'plugins' => ['legend' => ['display' => false]],
        ]);

        // Graphique PIE - Budget total par sponsor (top 5)
        $sponsors = $sponsorRepo->findAll();
        $topSponsors = array_slice($sponsors, 0, 5);
        
        $labels = [];
        $budgets = [];
        foreach ($topSponsors as $sponsor) {
            $labels[] = $sponsor->getNom();
            $budgets[] = $sponsor->getBudget();
        }

        $chartBudget = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chartBudget->setData([
            'labels' => $labels,
            'datasets' => [[
                'data' => $budgets,
                'backgroundColor' => [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                ],
            ]],
        ]);

        return $this->render('dashboard/index.html.twig', [
            'chartSponsors' => $chartSponsors,
            'chartBudget' => $chartBudget,
            'totalSponsors' => $totalSponsors,
            'totalContrats' => $totalContrats,
        ]);
    }
}
