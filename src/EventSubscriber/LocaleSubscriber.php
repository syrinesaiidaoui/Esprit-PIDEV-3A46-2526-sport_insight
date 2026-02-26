<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private const SUPPORTED = ['fr', 'en'];
    private const DEFAULT_LOCALE = 'fr';

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request) {
            return;
        }

        $session = $request->getSession();
        $locale = $session?->get('_locale', self::DEFAULT_LOCALE);

        $queryLocale = $request->query->get('_locale');
        if ($queryLocale && in_array($queryLocale, self::SUPPORTED, true)) {
            $locale = $queryLocale;
        }

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT_LOCALE;
        }

        if ($session) {
            $session->set('_locale', $locale);
        }

        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
