<?php

namespace App\Tests\Service;

use App\Service\NutritionService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class NutritionServiceTest extends TestCase
{
    private function makeService(): NutritionService
    {
        $client = new MockHttpClient(function () {
            return new MockResponse(json_encode([]), [
                'http_code' => 200,
                'headers' => ['content-type' => 'application/json'],
            ]);
        });

        return new NutritionService($client);
    }

    public function testGenerateNutritionAdviceReturnsExpectedKeys(): void
    {
        $service = $this->makeService();

        $advice = $service->generateNutritionAdvice(12, 12, 12, 'football');

        self::assertArrayHasKey('macros', $advice);
        self::assertArrayHasKey('hydration', $advice);
        self::assertArrayHasKey('meals', $advice);
        self::assertArrayHasKey('calories_estimate', $advice);
        self::assertArrayHasKey('api_foods', $advice);
    }

    public function testHydrationAdviceMatchesIntensity(): void
    {
        $service = $this->makeService();

        $advice = $service->generateNutritionAdvice(5, 6, 7, 'natation');
        $hydration = $advice['hydration'];

        self::assertGreaterThanOrEqual(1.5, $hydration['litres_min']);
        self::assertGreaterThanOrEqual($hydration['litres_min'], $hydration['litres_max']);
    }

    public function testCaloriesEstimateVariesWithIntensity(): void
    {
        $service = $this->makeService();

        $light = $service->generateNutritionAdvice(5, 5, 5, 'football')['calories_estimate'];
        $intense = $service->generateNutritionAdvice(18, 18, 18, 'football')['calories_estimate'];

        self::assertTrue($light['max'] < $intense['max']);
        self::assertSame('light', $light['intensity']);
        self::assertSame('intense', $intense['intensity']);
    }
}
