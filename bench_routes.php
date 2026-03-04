<?php

$endpoints = [
    '/',
    '/equipement/',
    '/api/products',
    '/api/products?perPage=20',
    '/api/products/1',
];
$base = 'http://127.0.0.1:8000';
$total = 30;

function pct(array $arr, float $p): float
{
    if (!$arr) return 0.0;
    $n = count($arr);
    $idx = max(min((int)round($p / 100 * $n) - 1, $n - 1), 0);
    return round($arr[$idx], 2);
}

foreach ($endpoints as $ep) {
    $times = [];
    $codes = [];
    for ($i = 0; $i < $total; $i++) {
        $ch = curl_init($base . $ep);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
        ]);
        $start = microtime(true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE) ?: 0;
        curl_close($ch);
        $times[] = (microtime(true) - $start) * 1000;
        $codes[] = $code;
    }
    sort($times);
    $avg = round(array_sum($times) / max(count($times), 1), 2);
    $success = count(array_filter($codes, fn ($c) => $c >= 200 && $c < 400));
    $fail = count($codes) - $success;
    $rps = round(count($codes) / (array_sum($times) / 1000), 2);
    $max = round(max($times), 2);

    $result = [
        'path' => $ep,
        'requests' => count($codes),
        'success' => $success,
        'fail' => $fail,
        'rps' => $rps,
        'avg_ms' => $avg,
        'p50_ms' => pct($times, 50),
        'p95_ms' => pct($times, 95),
        'p99_ms' => pct($times, 99),
        'max_ms' => $max,
    ];

    echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
}
