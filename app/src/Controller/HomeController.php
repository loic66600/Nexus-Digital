<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $produitsRepository;
    private $categorieRepository;

    public function __construct(ProduitsRepository $produitsRepository, CategorieRepository $categorieRepository)
    {
        $this->produitsRepository = $produitsRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/', name: 'home_index', defaults: ['category' => null])]
    public function index(?string $category): Response
    {
        if ($category) {
            // Remplacez les tirets bas par des espaces pour correspondre aux noms dans la base de données
            $category = str_replace('_', ' ', $category);
            $produits = $this->produitsRepository->findByCategoryName($category);
        } else {
            // Récupérer tous les produits si aucune catégorie n'est spécifiée
            $produits = $this->produitsRepository->AllProduit();
        }

        // Récupérer toutes les catégories pour l'affichage des onglets
        $categories = $this->categorieRepository->findAll();

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'selectedCategory' => $category,
            'categories' => $categories,
            'panier' => $panier,
        ]);
    }

    #[Route('/home-categorie/{id}', name: 'app_home_category')]
    public function category(int $id): Response
    {
        // Récupérer la catégorie sélectionnée
        $category = $this->categorieRepository->findOneById($id);
        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        // Récupérer tous les produits de la catégorie sélectionnée
        $produits = $this->produitsRepository->findByCategoryName($category->getName());

        // Récupérer toutes les catégories pour l'affichage des onglets
        $categories = $this->categorieRepository->findAll();

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'selectedCategory' => $category->getName(),
            'categories' => $categories,
            'panier' => $panier,
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