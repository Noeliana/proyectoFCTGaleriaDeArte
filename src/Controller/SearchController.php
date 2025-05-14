<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\ArtistRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\TagRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/buscar', name: 'app_search')]
    public function search(
        Request $request,
        ImageRepository $imageRepo,
        ArtistRepository $artistRepo,
        TagRepository $tagRepo,
        CategoryRepository $categoryRepo,
        SubcategoryRepository $subRepo
    ): Response {
        $query = $request->query->get('q');
        $tagSlug = $request->query->get('tag');

        if ($tagSlug) {
            $tag = $tagRepo->findOneBy(['slug' => $tagSlug]);
            if ($tag) {
                return $this->redirectToRoute('app_tag_show', ['slug' => $tagSlug]);
            }
        }

        $image = $imageRepo->createQueryBuilder('i')
            ->where('LOWER(i.title) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($image) {
            return $this->redirectToRoute('app_image_show', ['id' => $image->getId()]);
        }

        $artist = $artistRepo->createQueryBuilder('a')
            ->where('LOWER(a.name) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($artist) {
            return $this->redirectToRoute('app_artista_show', ['id' => $artist->getId()]);
        }

        $category = $categoryRepo->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($category) {
            return $this->redirectToRoute('app_categoria_show', ['slug' => $category->getSlug()]);
        }

        $subcategory = $subRepo->createQueryBuilder('s')
            ->where('LOWER(s.name) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($subcategory) {
            return $this->redirectToRoute('app_subcategoria_show', ['id' => $subcategory->getId()]);
        }

        $this->addFlash('warning', 'No se encontraron resultados para "' . $query . '".');
        return $this->redirectToRoute('app_home');
    }
}