<?php

namespace App\Twig;

use App\Service\MediaPathResolver;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MediaExtension extends AbstractExtension
{
    public function __construct(
        private readonly MediaPathResolver $mediaPathResolver,
        private readonly Packages $packages,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_url', [$this, 'mediaUrl']),
        ];
    }

    public function mediaUrl(?string $storedValue, string $type): ?string
    {
        $resolved = $this->mediaPathResolver->resolve($storedValue, $type);
        if ($resolved === null) {
            return null;
        }

        return match ($resolved['kind']) {
            'url' => $resolved['value'],
            'asset' => $this->packages->getUrl($resolved['value']),
            'proxy' => $this->urlGenerator->generate('app_shared_media_show', [
                'type' => $type,
                'encodedPath' => $resolved['value'],
            ]),
        };
    }
}
