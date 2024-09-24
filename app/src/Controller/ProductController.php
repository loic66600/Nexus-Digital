<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $produitsRepository;

    public function __construct(ProduitsRepository $produitsRepository)
    {
        $this->produitsRepository = $produitsRepository;
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(int $id, Request $request): Response
    {
        // Récupérer les détails du produit par son ID
        $productDetails = $this->produitsRepository->findProductDetails($id);

        if (!$productDetails) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        // Récupérer les produits associés (exemple : même catégorie)
        $associatedProducts = [];
        if ($productDetails->getCategories()->count() > 0) {
            $categoryName = $productDetails->getCategories()->first()->getName();
            $associatedProducts = $this->produitsRepository->findByCategoryName($categoryName);

            // Exclure le produit actuel des produits associés
            $associatedProducts = array_filter($associatedProducts, function ($product) use ($id) {
                return $product->getId() !== $id;
            });
        }

        // Calculer la quantité totale en stock
        $totalStockQuantity = array_reduce($productDetails->getStocks()->toArray(), function ($carry, $stock) {
            return $carry + $stock->getQuantity();
        }, 0);

        // Calculer la note moyenne des avis
        $avis = $productDetails->getAvis();
        $totalNotes = array_reduce($avis->toArray(), function ($carry, $avi) {
            return $carry + $avi->getNote();
        }, 0);
        
        $averageRating = count($avis) > 0 ? round($totalNotes / count($avis), 1) : 0;

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        // Récupérer le nombre d'articles dans la wishlist à partir de la session
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        $wishlistCount = count($wishlistIds);

        // Rendre la vue avec toutes les données nécessaires
        return $this->render('product/index.html.twig', [
            'product' => $productDetails,
            'averageRating' => $averageRating,
            'totalStockQuantity' => $totalStockQuantity,
            'associatedProducts' => $associatedProducts,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount, // Utiliser la variable avec le $
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