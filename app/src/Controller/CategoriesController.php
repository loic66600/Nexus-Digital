<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    private $categorieRepository;
    private $produitsRepository;

    public function __construct(CategorieRepository $categorieRepository, ProduitsRepository $produitsRepository)
    {
        $this->categorieRepository = $categorieRepository;
        $this->produitsRepository = $produitsRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();
        // dd($categories);

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
            'menuCategories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function show(int $id): Response
    {
        // Récupérer la catégorie par ID
        $category = $this->categorieRepository->findOneByIdWithProducts($id);
       

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);

        }
        $cat[] =$category;

        return $this->render('categories/index.html.twig', [
            'categories' => $cat,
            'menuCategories' => $this->categorieRepository->findAll(), // Pour le menu de navigation
        ]);
    }
}