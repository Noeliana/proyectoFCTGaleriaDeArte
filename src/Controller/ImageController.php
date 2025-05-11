<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\SubcategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;


#[Route('/image')]
final class ImageController extends AbstractController
{
    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        SubcategoryRepository $subRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $categoryId = $request->query->get('categoryId');
        $subcategoriaId = $request->query->get('subcategoriaId');

        $category = $categoryRepository->find($categoryId);
        $subcategoria = $subcategoriaId ? $subRepo->find($subcategoriaId) : null;

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile) {
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $image->setImageFile($newFilename);
            }

            $image->setOwner($this->getUser());
            $image->setCreatedAt(new \DateTimeImmutable());
            $image->setCategory($category);

            if ($subcategoria) {
                $image->setSubCategory($subcategoria);
            }

            foreach ($image->getTags() as $tag) {
                $tag->addImage($image);
            }

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('app_image_show', [
                'id' => $image->getId(),
                'from' => $subcategoria ? 'subcategoria' : 'category',
                'slug' => $subcategoria ? $subcategoria->getId() : $category->getSlug(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
            'category' => $category,
            'subcategoria' => $subcategoria,
        ]);
    }

    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(Request $request, Image $image, SubcategoryRepository $subRepo, TagRepository $tagRepo): Response
    {
        $from = $request->query->get('from');
        $slug = $request->query->get('slug');
        $artist = $image->getArtist();

        $subcategoria = null;
        $tag = null;

        if ($from === 'subcategoria' && $slug) {
            $subcategoria = $subRepo->find($slug);
        }

        if ($from === 'tag' && $slug) {
            $tag = $tagRepo->findOneBy(['slug' => $slug]);
        }


        return $this->render('image/show.html.twig', [
            'image' => $image,
            'from' => $from,
            'slug' => $slug,
            'artist' => $artist,
            'subcategoria' => $subcategoria,
            'tag' => $tag,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, EntityManagerInterface $entityManager, CategoryRepository $categoryRepo, SubcategoryRepository $subRepo): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $image->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para editar esta imagen.');
        }

        $category = $image->getCategory();

        $from = $request->query->get('from');
        $slug = $request->query->get('slug');

        $subcategoria = null;
        if ($from === 'subcategoria' && $slug) {
            $subcategoria = $subRepo->find($slug);
        }

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_image_show', [
                'id' => $image->getId(),
                'from' => $from,
                'slug' => $slug,
            ]);
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'form' => $form,
            'category' => $category,
            'subcategoria' => $subcategoria,
            'from' => $from,
            'slug' => $slug,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $image->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('No tienes permiso para eliminar esta imagen.');
        }

        $slug = $request->request->get('slug');
        $from = $request->request->get('from');

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        if ($from === 'subcategoria' && $slug) {
            return $this->redirectToRoute('app_subcategoria_show', [
                'id' => $slug,
            ]);
        }

        if ($from === 'category' && $slug) {
            return $this->redirectToRoute('app_categoria_show', [
                'slug' => $slug,
            ]);
        }

        return $this->redirectToRoute('app_home');
    }



    #[Route('/image/{id}/like', name: 'app_image_like', methods: ['POST'])]
    public function like(Image $image, EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $request->getSession();
        $likedImages = $session->get('liked_images', []);

        $imageId = $image->getId();

        if (in_array($imageId, $likedImages)) {
            $image->setLikes(max(0, $image->getLikes() - 1));
            $likedImages = array_diff($likedImages, [$imageId]);
        } else {
            $image->setLikes($image->getLikes() + 1);
            $likedImages[] = $imageId;
        }

        $session->set('liked_images', $likedImages);

        $entityManager->persist($image);
        $entityManager->flush();

        return $this->redirectToRoute('app_image_show', [
            'id' => $imageId,
            'from' => $request->query->get('from'),
            'slug' => $request->query->get('slug')
        ]);

    }

}
