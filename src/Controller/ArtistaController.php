<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $usersWithImages = $userRepository->findUsersWithImages();

        return $this->render('artista/index.html.twig', [
            'artists' => $artists,
            'user_artists' => $usersWithImages,
        ]);
    }
    #[Route('/artista/new', name: 'app_artista_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('artists_directory'),
                    $newFilename
                );
                $artist->setImage($newFilename);
            }

            $entityManager->persist($artist);
            $entityManager->flush();

            return $this->redirectToRoute('app_artista');
        }

        return $this->render('artista/new.html.twig', [
            'form' => $form,
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
    #[Route('/artista/{id}/delete', name: 'app_artista_delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $artist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_artista');
    }
    #[Route('/artista/{id}/edit', name: 'app_artista_edit')]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('artists_directory'),
                    $newFilename
                );

                $artist->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_artista_show', ['id' => $artist->getId()]);
        }

        return $this->render('artista/edit.html.twig', [
            'form' => $form,
            'artist' => $artist,
        ]);
    }



}
