<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\CategorieRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();
        // dd($categories);
        $categoriesWithRatings = $this->addRatingsToCategories($categories);
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);

        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesWithRatings,
            'menuCategories' => $categories,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function show(int $id, Request $request): Response
    {
        $category = $this->categorieRepository->findOneByIdWithProducts($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $categoryWithRatings = $this->addRatingsToCategories([$category])[0];
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);

        return $this->render('categories/index.html.twig', [
            'categories' => [$categoryWithRatings],
            'menuCategories' => $this->categorieRepository->findAll(),
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
        ]);
    }

    private function getPanier()
    {
        if ($user = $this->getUser()) {
            return method_exists($user, 'getPaniers') ? $user->getPaniers()->last() : null;
        }
        return null;
    }

    private function getWishlistCount(Request $request): int
    {
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        return count($wishlistIds);
    }

    private function calculateAverageRating($product): float
    {
        $avis = $product->getAvis()->filter(function(Avis $avi) {
            return $avi->isValide();
        });
        $totalNotes = array_reduce($avis->toArray(), function ($carry, Avis $avi) {
            return $carry + $avi->getNote();
        }, 0);
        return count($avis) > 0 ? round($totalNotes / count($avis), 1) : 0;
    }

    private function addRatingsToCategories(array $categories): array
    {
        $categoriesWithRatings = [];
        foreach ($categories as $category) {
            $produitsWithRatings = [];
            foreach ($category->getProduits() as $produit) {
                $produitsWithRatings[] = [
                    'produit' => $produit,
                    'averageRating' => $this->calculateAverageRating($produit)
                ];
            }
            $categoriesWithRatings[] = [
                'category' => $category,
                'produitsWithRatings' => $produitsWithRatings
            ];
        }
        return $categoriesWithRatings;
    }

    
}