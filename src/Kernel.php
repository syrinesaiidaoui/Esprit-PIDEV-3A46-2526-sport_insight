<?php

namespace App;

use App\DependencyInjection\Compiler\LightweightDoctrineDoctorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        $isConsole = \in_array(\PHP_SAPI, ['cli', 'phpdbg'], true);
        $skipOnHttp = [
            'Symfony\\Bundle\\MakerBundle\\MakerBundle',
        ];

        foreach ($contents as $class => $envs) {
            if (($envs[$this->environment] ?? $envs['all'] ?? false) !== true) {
                continue;
            }

            if (!$isConsole && \in_array($class, $skipOnHttp, true)) {
                continue;
            }

            yield new $class();
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        if ('dev' === $this->getEnvironment()) {
            $container->addCompilerPass(new LightweightDoctrineDoctorPass());
        }
    }
}
