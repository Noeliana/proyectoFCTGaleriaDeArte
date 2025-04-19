<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistaController extends AbstractController
{
    #[Route('/artista', name: 'app_artista')]
    public function index(): Response
    {
        return $this->render('artista/index.html.twig', [
            'controller_name' => 'ArtistaController',
        ]);
    }
}
