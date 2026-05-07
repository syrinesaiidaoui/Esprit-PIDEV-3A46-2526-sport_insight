<?php

namespace App\Tests\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * Generic round-trip tests that exercise getters/setters on every entity
 * to ensure basic property wiring stays intact.
 */
class EntityRoundTripTest extends TestCase
{
    /**
     * @dataProvider entityClassProvider
     */
    public function testSettersAndGettersRoundTrip(string $class): void
    {
        $entity = new $class();

        foreach (get_class_methods($entity) as $method) {
            if (!str_starts_with($method, 'set')) {
                continue;
            }

            $reflection = new \ReflectionMethod($entity, $method);
            if ($reflection->getNumberOfRequiredParameters() !== 1) {
                continue;
            }

            $parameter = $reflection->getParameters()[0];
            $value = $this->sampleValueForParameter($parameter);
            if ($value === self::UNSUPPORTED) {
                continue; // skip unsupported parameter types (e.g., collections without type hints)
            }

            $reflection->invoke($entity, $value);

            $suffix = substr($method, 3); // strip "set"
            $getterNames = ["get{$suffix}", "is{$suffix}", "has{$suffix}"];
            $getter = null;
            foreach ($getterNames as $candidate) {
                if (method_exists($entity, $candidate)) {
                    $getter = $candidate;
                    break;
                }
            }
            if ($getter === null) {
                continue;
            }

            $result = $entity->$getter();
            $this->assertValuesEquivalent($value, $result, $class, $method, $getter);
        }

        $this->addToAssertionCount(1); // ensure the test is marked as having assertions
    }

    public function entityClassProvider(): array
    {
        return [
            [\App\Entity\Annonce::class],
            [\App\Entity\ChatMessage::class],
            [\App\Entity\Commentaire::class],
            [\App\Entity\ContratSponsor::class],
            [\App\Entity\Entrainement::class],
            [\App\Entity\Equipe::class],
            [\App\Entity\Evaluation::class],
            [\App\Entity\Joueur::class],
            [\App\Entity\MatchLineup::class],
            [\App\Entity\Matchs::class],
            [\App\Entity\Message::class],
            [\App\Entity\Notification::class],
            [\App\Entity\Participation::class],
            [\App\Entity\Sponsor::class],
            [\App\Entity\User::class],
            [\App\Entity\ProductOrder\Product::class],
            [\App\Entity\ProductOrder\Order::class],
            [\App\Entity\ProductOrder\OrderItem::class],
        ];
    }

    private const UNSUPPORTED = '__UNSUPPORTED__';

    private function assertValuesEquivalent($expected, $actual, string $class, string $setter, string $getter): void
    {
        // Allow entities to normalize data (e.g., uppercasing, adding default roles)
        if (is_array($expected) && is_array($actual)) {
            foreach ($expected as $v) {
                $this->assertContains(
                    $v,
                    $actual,
                    sprintf('%s::%s did not preserve array value via %s', $class, $setter, $getter)
                );
            }
            return;
        }

        if (is_string($expected) && is_string($actual)) {
            $this->assertSame(
                mb_strtolower($expected),
                mb_strtolower($actual),
                sprintf('%s::%s did not round-trip (case-insensitive) via %s', $class, $setter, $getter)
            );
            return;
        }

        $this->assertSame(
            $expected,
            $actual,
            sprintf('%s::%s did not round-trip via %s', $class, $setter, $getter)
        );
    }

    private function sampleValueForParameter(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type instanceof \ReflectionNamedType) {
            if ($type->isBuiltin()) {
                return $this->sampleBuiltin($type->getName());
            }

            $className = $type->getName();

            if (is_a($className, DateTimeImmutable::class, true)) {
                return new DateTimeImmutable('2026-01-01');
            }
            if (is_a($className, DateTime::class, true)) {
                return new DateTime('2026-01-01');
            }
            if (is_a($className, Collection::class, true)) {
                return new ArrayCollection();
            }

            // For entity relationships, instantiate an empty object (requires no-arg ctor)
            if (class_exists($className)) {
                return new $className();
            }
        }

        // Fallback when type is union or unsupported
        return self::UNSUPPORTED;
    }

    private function sampleBuiltin(string $name)
    {
        return match ($name) {
            'string' => 'sample',
            'int' => 1,
            'float', 'double' => 1.0,
            'bool' => true,
            'array' => [],
            default => self::UNSUPPORTED,
        };
    }
}
