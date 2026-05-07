<?php

namespace App\Controller;

use App\Service\MediaPathResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

final class SharedMediaController extends AbstractController
{
    #[Route('/media/shared/{type}/{encodedPath}', name: 'app_shared_media_show', methods: ['GET'], requirements: ['type' => 'equipes|joueurs', 'encodedPath' => '[A-Za-z0-9\-_]+'])]
    public function show(string $type, string $encodedPath, MediaPathResolver $mediaPathResolver): BinaryFileResponse
    {
        $absolutePath = $mediaPathResolver->decodeProxyPath($encodedPath, $type);
        if ($absolutePath === null) {
            throw $this->createNotFoundException('Media not found.');
        }

        $response = new BinaryFileResponse($absolutePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($absolutePath));
        $response->setAutoEtag();
        $response->setAutoLastModified();
        $response->setPublic();
        $response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        $response->headers->addCacheControlDirective('immutable');

        return $response;
    }
}
