<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArtistRepository;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;

final class ArtistaController extends AbstractController
{
    #[Route('/artista', name: 'app_artista')]
    public function index(ArtistRepository $artistRepository, UserRepository $userRepository, ImageRepository $imageRepository): Response
    {
        $artists = $artistRepository->findAll();

        // Buscar usuarios que hayan subido al menos 1 imagen
        $usersWithImages = $userRepository->findUsersWithImages();

        return $this->render('artista/index.html.twig', [
            'artists' => $artists,
            'user_artists' => $usersWithImages,
        ]);
    }
    #[Route('/artista/{id}', name: 'app_artista_show')]
    public function show(int $id, ArtistRepository $artistRepository): Response
    {
        $artist = $artistRepository->find($id);

        if (!$artist) {
            throw $this->createNotFoundException('El artista no fue encontrado.');
        }

        return $this->render('artista/show.html.twig', [
            'artist' => $artist,
        ]);
    }

}
