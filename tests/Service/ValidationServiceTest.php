<?php

namespace App\Tests\Service;

use App\Service\ValidationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationServiceTest extends TestCase
{
    private function makeService(): ValidationService
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        return new ValidationService($validator);
    }

    public function testProductValidationDetectsMissingMandatoryFields(): void
    {
        $service = $this->makeService();

        $errors = $service->validateProductData([
            'name' => '',
            'price' => null,
            'stock' => null,
        ]);

        self::assertArrayHasKey('name', $errors);
        self::assertArrayHasKey('price', $errors);
        self::assertArrayHasKey('stock', $errors);
    }

    public function testProductValidationRejectsLongValuesAndNegativeNumbers(): void
    {
        $service = $this->makeService();

        $errors = $service->validateProductData([
            'name' => str_repeat('a', 260),
            'price' => -5,
            'stock' => -1,
            'category' => str_repeat('c', 300),
            'size' => str_repeat('L', 20),
            'brand' => str_repeat('B', 40),
        ]);

        self::assertArrayHasKey('name', $errors);
        self::assertArrayHasKey('price', $errors);
        self::assertArrayHasKey('stock', $errors);
        self::assertArrayHasKey('category', $errors);
        self::assertArrayHasKey('size', $errors);
        self::assertArrayHasKey('brand', $errors);
    }

    public function testOrderValidationDetectsFutureDateAndInvalidStatus(): void
    {
        $service = $this->makeService();

        $errors = $service->validateOrderData([
            'quantity' => 2,
            'orderDate' => (new \DateTime('+1 day'))->format('Y-m-d'),
            'status' => 'unknown',
            'product' => null,
            'entraineur' => null,
        ]);

        self::assertArrayHasKey('orderDate', $errors);
        self::assertArrayHasKey('status', $errors);
        self::assertArrayHasKey('product', $errors);
        self::assertArrayHasKey('entraineur', $errors);
    }

    public function testOrderValidationAcceptsValidPayload(): void
    {
        $service = $this->makeService();

        $errors = $service->validateOrderData([
            'quantity' => 3,
            'orderDate' => (new \DateTime('-1 day'))->format('Y-m-d'),
            'status' => 'confirmed',
            'product' => 42,
            'entraineur' => 7,
        ]);

        self::assertSame([], $errors);
    }
}
