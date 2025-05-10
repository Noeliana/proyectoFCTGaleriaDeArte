<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ImageRepository $imageRepository): Response
    {
        $topImages = $imageRepository->findBy([], ['likes' => 'DESC'], 6);

        return $this->render('home/inicio.html.twig', [
            'topImages' => $topImages,
        ]);
    }
}
