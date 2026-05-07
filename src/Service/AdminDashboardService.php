<?php

namespace App\Service;

use App\Entity\Equipe;
use App\Entity\Matchs;
use App\Entity\ProductOrder\Order;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use App\Repository\MatchsRepository;
use App\Repository\OrderRepository;

class AdminDashboardService
{
    private const REVENUE_STATUSES = ['confirmed', 'shipped', 'delivered'];

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EquipeRepository $equipeRepository,
        private readonly JoueurRepository $joueurRepository,
        private readonly MatchsRepository $matchsRepository
    )
    {
    }

    public function buildDashboardData(): array
    {
        $orders = $this->orderRepository
            ->createQueryBuilder('o')
            ->leftJoin('o.product', 'p')
            ->addSelect('p')
            ->orderBy('o.orderDate', 'ASC')
            ->getQuery()
            ->getResult();

        $statusCounts = [
            'pending' => 0,
            'confirmed' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'rejected' => 0,
        ];

        $overview = [
            'totalSales' => 0,
            'totalRevenue' => 0.0,
            'totalOrders' => count($orders),
            'confirmedOrders' => 0,
            'pendingOrders' => 0,
        ];

        $daily = $this->initDailyBuckets(14);
        $monthly = $this->initMonthlyBuckets(6);
        $productSales = [];

        foreach ($orders as $order) {
            if (!$order instanceof Order) {
                continue;
            }

            $status = (string) $order->getStatus();
            $quantity = $this->resolveSalesQuantity($order);
            $lineTotal = $order->getComputedTotal();
            $orderDate = $order->getOrderDate();

            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }

            if ($status === 'confirmed') {
                $overview['confirmedOrders']++;
            }
            if ($status === 'pending') {
                $overview['pendingOrders']++;
            }

            $isRevenueStatus = in_array($status, self::REVENUE_STATUSES, true);
            if ($isRevenueStatus) {
                $overview['totalSales'] += $quantity;
                $overview['totalRevenue'] += $lineTotal;

                // Legacy single-product orders
                $productName = $order->getProduct()?->getName();
                if ($productName) {
                    $productSales[$productName] = $productSales[$productName] ?? [
                        'name' => $productName,
                        'quantity' => 0,
                        'revenue' => 0.0,
                    ];
                    $productSales[$productName]['quantity'] += $quantity;
                    $productSales[$productName]['revenue'] += $lineTotal;
                }

                // Itemized orders
                foreach ($order->getItems() as $item) {
                    $p = $item->getProduct();
                    if (!$p) {
                        continue;
                    }
                    $name = $p->getName() ?? 'Produit';
                    $qty = max(0, (int) $item->getQuantity());
                    $line = $item->getUnitPrice() ? $qty * (float) $item->getUnitPrice() : 0.0;

                    $productSales[$name] = $productSales[$name] ?? [
                        'name' => $name,
                        'quantity' => 0,
                        'revenue' => 0.0,
                    ];
                    $productSales[$name]['quantity'] += $qty;
                    $productSales[$name]['revenue'] += $line;
                }
            }

            if ($orderDate instanceof \DateTimeInterface) {
                $dayKey = $orderDate->format('Y-m-d');
                if (isset($daily[$dayKey])) {
                    $daily[$dayKey]['orders']++;
                    if ($isRevenueStatus) {
                        $daily[$dayKey]['sales'] += $quantity;
                        $daily[$dayKey]['revenue'] += $lineTotal;
                    }
                }

                $monthKey = $orderDate->format('Y-m');
                if (isset($monthly[$monthKey])) {
                    $monthly[$monthKey]['orders']++;
                    if ($isRevenueStatus) {
                        $monthly[$monthKey]['sales'] += $quantity;
                        $monthly[$monthKey]['revenue'] += $lineTotal;
                    }
                }
            }
        }

        $overview['totalRevenue'] = round($overview['totalRevenue'], 2);
        usort($productSales, static fn (array $a, array $b): int => $b['quantity'] <=> $a['quantity']);

        $clubData = $this->buildClubData();

        return [
            'overview' => $overview,
            'statusBreakdown' => $statusCounts,
            'dailyStats' => array_values($daily),
            'monthlyStats' => array_values($monthly),
            'topSellingProducts' => array_slice($productSales, 0, 5),
            'clubOverview' => $clubData['overview'],
            'playersByTeam' => $clubData['playersByTeam'],
            'matchStatusClub' => $clubData['matchStatus'],
            'recentMatches' => $clubData['recentMatches'],
        ];
    }

    private function buildClubData(): array
    {
        $teams = $this->equipeRepository->findAll();
        $players = $this->joueurRepository->findAll();
        $matches = $this->matchsRepository->createQueryBuilder('m')
            ->leftJoin('m.equipeDomicile', 'home')
            ->leftJoin('m.equipeExterieur', 'away')
            ->addSelect('home', 'away')
            ->orderBy('m.dateMatch', 'DESC')
            ->addOrderBy('m.heureDebut', 'DESC')
            ->getQuery()
            ->getResult();

        $today = new \DateTimeImmutable('today');
        $upcomingMatches = 0;
        $matchStatus = [];
        $recentMatches = [];

        foreach ($matches as $match) {
            if (!$match instanceof Matchs) {
                continue;
            }

            $status = strtolower(trim((string) ($match->getStatut() ?: 'unknown')));
            if ($status === '') {
                $status = 'unknown';
            }
            $matchStatus[$status] = ($matchStatus[$status] ?? 0) + 1;

            $date = $match->getDateMatch();
            if ($date instanceof \DateTimeInterface) {
                $matchDate = new \DateTimeImmutable($date->format('Y-m-d'));
                if ($matchDate >= $today) {
                    $upcomingMatches++;
                }
            }

            if (count($recentMatches) < 8) {
                $recentMatches[] = [
                    'id' => $match->getId(),
                    'date' => $date,
                    'label' => sprintf(
                        '%s vs %s',
                        (string) ($match->getEquipeDomicile()?->getNom() ?: 'N/A'),
                        (string) ($match->getEquipeExterieur()?->getNom() ?: 'N/A')
                    ),
                    'score' => sprintf(
                        '%d - %d',
                        (int) ($match->getScoreEquipeDomicile() ?? 0),
                        (int) ($match->getScoreEquipeExterieur() ?? 0)
                    ),
                    'status' => ucfirst($status),
                    'location' => (string) ($match->getLieu() ?: 'N/A'),
                ];
            }
        }

        $playersByTeam = [];
        foreach ($teams as $team) {
            if (!$team instanceof Equipe) {
                continue;
            }

            $playersByTeam[] = [
                'name' => (string) ($team->getNom() ?: 'Team'),
                'count' => $team->getJoueurs()->count(),
            ];
        }

        usort($playersByTeam, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);
        $playersByTeam = array_slice($playersByTeam, 0, 8);
        arsort($matchStatus, SORT_NUMERIC);

        return [
            'overview' => [
                'totalTeams' => count($teams),
                'totalPlayers' => count($players),
                'totalMatches' => count($matches),
                'upcomingMatches' => $upcomingMatches,
            ],
            'playersByTeam' => $playersByTeam,
            'matchStatus' => $matchStatus,
            'recentMatches' => $recentMatches,
        ];
    }

    private function initDailyBuckets(int $days): array
    {
        $buckets = [];
        $today = new \DateTimeImmutable('today');

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->modify(sprintf('-%d days', $i));
            $key = $date->format('Y-m-d');
            $buckets[$key] = [
                'key' => $key,
                'label' => $date->format('d M'),
                'orders' => 0,
                'sales' => 0,
                'revenue' => 0.0,
            ];
        }

        return $buckets;
    }

    private function initMonthlyBuckets(int $months): array
    {
        $buckets = [];
        $currentMonth = new \DateTimeImmutable('first day of this month');

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentMonth->modify(sprintf('-%d months', $i));
            $key = $date->format('Y-m');
            $buckets[$key] = [
                'key' => $key,
                'label' => $date->format('M Y'),
                'orders' => 0,
                'sales' => 0,
                'revenue' => 0.0,
            ];
        }

        return $buckets;
    }

    private function resolveSalesQuantity(Order $order): int
    {
        if (!$order->getItems()->isEmpty()) {
            $sum = 0;
            foreach ($order->getItems() as $item) {
                $sum += max(0, (int) $item->getQuantity());
            }
            return $sum;
        }

        return max(0, (int) $order->getQuantity());
    }
}
