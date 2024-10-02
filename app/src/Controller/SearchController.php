<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\ProduitsRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $produitsRepository;
    private $categorieRepository;

    public function __construct(ProduitsRepository $produitsRepository, CategorieRepository $categorieRepository)
    {
        $this->produitsRepository = $produitsRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/search', name: 'search_products')]
    public function searchProducts(Request $request): Response
    {
        $query = $request->query->get('query');
    
        // Recherchez uniquement par nom
        $produits = $this->produitsRepository->searchByName($query);
        
        $produitsWithRatings = $this->addRatingsToProducts($produits);
        $categories = $this->categorieRepository->findAll();
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);
    
        return $this->render('search/index.html.twig', [
            'produits' => $produitsWithRatings,
            'query' => $query,
            'categories' => $categories,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
        ]);

        
    }    private function addRatingsToProducts(array $produits): array
    {
        $produitsWithRatings = [];
        foreach ($produits as $produit) {
            $produitsWithRatings[] = [
                'produit' => $produit,
                'averageRating' => $this->calculateAverageRating($produit)
            ];
        }
        return $produitsWithRatings;
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

    
}