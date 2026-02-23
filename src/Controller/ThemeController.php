<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme/toggle', name: 'app_theme_toggle', methods: ['GET'])]
    public function toggle(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $currentTheme = $session->get('theme_mode', 'light');
        $nextTheme = $currentTheme === 'dark' ? 'light' : 'dark';

        $session->set('theme_mode', $nextTheme);

        return $this->redirectToReferrer($request);
    }

    #[Route('/theme/{mode}', name: 'app_theme_set', methods: ['GET'], requirements: ['mode' => 'light|dark'])]
    public function setMode(Request $request, string $mode): RedirectResponse
    {
        $request->getSession()->set('theme_mode', $mode);

        return $this->redirectToReferrer($request);
    }

    private function redirectToReferrer(Request $request): RedirectResponse
    {
        $referrer = $request->headers->get('referer');
        if (\is_string($referrer) && $referrer !== '') {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('front_home');
    }
}
