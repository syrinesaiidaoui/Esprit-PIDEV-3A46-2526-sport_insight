<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LightweightDoctrineDoctorPass implements CompilerPassInterface
{
    /**
     * Keep only lightweight analyzers for smoother dev requests.
     *
     * @var array<string, bool>
     */
    private const ALLOWED_ANALYZERS = [
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Performance\\NPlusOneAnalyzer' => true,
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Performance\\SlowQueryAnalyzer' => true,
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Performance\\FindAllAnalyzer' => true,
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Performance\\LazyLoadingAnalyzer' => true,
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Security\\DQLInjectionAnalyzer' => true,
        'AhmedBhs\\DoctrineDoctor\\Analyzer\\Security\\SQLInjectionInRawQueriesAnalyzer' => true,
    ];

    public function process(ContainerBuilder $container): void
    {
        $taggedAnalyzers = $container->findTaggedServiceIds('doctrine_doctor.analyzer');

        foreach (array_keys($taggedAnalyzers) as $serviceId) {
            if (isset(self::ALLOWED_ANALYZERS[$serviceId])) {
                continue;
            }

            if ($container->hasDefinition($serviceId)) {
                $container->getDefinition($serviceId)->clearTag('doctrine_doctor.analyzer');
            }
        }
    }
}
