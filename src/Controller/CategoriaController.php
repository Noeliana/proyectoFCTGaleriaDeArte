<?php

namespace App\Controller;
use App\Repository\SubcategoryRepository;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoriaController extends AbstractController
{
    #[Route('/categoria', name: 'app_categoria_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('categoria/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categoria/{slug}', name: 'app_categoria_show')]
    public function show(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        return $this->render('categoria/show.html.twig', [
            'category' => $category,
            'images' => $category->getImages(),
            'subcategories' => $category->getSubcategories(),
        ]);
    }
    #[Route('/categoria/{slug}/subcategorias', name: 'app_categoria_subcategorias')]
    public function subcategorias(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        return $this->render('categoria/subcategorias.html.twig', [
            'category' => $category,
            'subcategories' => $category->getSubcategories(),
        ]);
    }
    #[Route('/subcategoria/{id}', name: 'app_subcategoria_show')]
    public function showSubcategoria(int $id, SubcategoryRepository $subRepo): Response
    {
        $subcategoria = $subRepo->find($id);

        if (!$subcategoria) {
            throw $this->createNotFoundException('Subcategoría no encontrada');
        }

        $category = $subcategoria->getCategory();

        return $this->render('categoria/show.html.twig', [
            'subcategoria' => $subcategoria,
            'category' => $category,
            'images' => $subcategoria->getImages(),
        ]);
    }




}
