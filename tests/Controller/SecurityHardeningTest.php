<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityHardeningTest extends WebTestCase
{
    public function testBackOfficeRouteIsNotPublicAnymore(): void
    {
        $client = static::createClient();
        $client->request('GET', '/back/annonce/');

        $response = $client->getResponse();
        self::assertContains($response->getStatusCode(), [302, 401, 403]);

        if ($response->getStatusCode() === 302) {
            self::assertStringContainsString('/login', (string) $response->headers->get('Location'));
        }
    }

    public function testApiProductMutationRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Blocked Product',
                'price' => 9.99,
                'stock' => 1,
            ], JSON_THROW_ON_ERROR)
        );

        $response = $client->getResponse();
        self::assertContains($response->getStatusCode(), [302, 401, 403]);

        if ($response->getStatusCode() === 302) {
            self::assertStringContainsString('/login', (string) $response->headers->get('Location'));
        }
    }
}
