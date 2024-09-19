<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;

class CategoriesController extends AbstractController
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();

        // Trouver la catégorie par défaut (par exemple, "Ordinateur")
        foreach ($categories as $category) {
            if ($category->getName() === 'Ordinateur') {
                return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
            }
        }

        // Si aucune catégorie par défaut n'est trouvée, afficher la liste des catégories
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function show(int $id): Response
    {
        // Récupérer la catégorie avec ses produits
        $category = $this->categorieRepository->findOneByIdWithProducts($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        return $this->render('categories/show.html.twig', [
            'category' => $category,
            'categories' => $this->categorieRepository->findAll(), // Passer toutes les catégories pour le menu
        ]);
    }
}