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

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
            'menuCategories' => $categories,
            'panier' => $panier, // Passer le panier au template
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

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        return $this->render('categories/index.html.twig', [
            'categories' => [$category],
            'menuCategories' => $this->categorieRepository->findAll(), // Pour le menu de navigation
            'panier' => $panier, // Passer le panier au template
        ]);
    }

    private function getPanier()
    {
        // Fonction pour récupérer le panier de l'utilisateur actuel
        if ($user = $this->getUser()) {
            return method_exists($user, 'getPaniers') ? $user->getPaniers()->last() : null;
        }
        return null;
    }
}