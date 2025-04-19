<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    #[Route('/change-language', name: 'app_change_language', methods: ['POST'])]
    public function changeLanguage(Request $request): RedirectResponse
    {
        $lang = $request->request->get('lang');

        // Aquí puedes guardar el idioma en sesión (ejemplo):
        $request->getSession()->set('_locale', $lang);

        // Redirige a la misma página u home
        return $this->redirectToRoute('app_home');
    }
}

