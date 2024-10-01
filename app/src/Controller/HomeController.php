<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\ProduitsRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(?string $category, Request $request): Response
    {
        if ($category) {
            $category = str_replace('_', ' ', $category);
            $produits = $this->produitsRepository->findByCategoryName($category);
        } else {
            $produits = $this->produitsRepository->AllProduit();
        }

        $produitsWithRatings = $this->addRatingsToProducts($produits);
        usort($produitsWithRatings, function ($a, $b) {
            return $b['averageRating'] <=> $a['averageRating'];
        });

        $categories = $this->categorieRepository->findAll();
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);

        return $this->render('home/index.html.twig', [
            'produits' => array_slice($produitsWithRatings, 0, 10), // Limite à 10 meilleurs produits
            'selectedCategory' => $category,
            'categories' => $categories,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
        ]);
    }

// Dans HomeController.php

// Dans HomeController.php

#[Route('/nouveaux-produits', name: 'nouveaux_produits')]
public function nouveauxProduits(): Response
{
    $produits = $this->produitsRepository->findAllOrderedByIdDesc();
    $produitsWithRatings = $this->addRatingsToProducts($produits);

    return $this->render('home/newproducte.html.twig', [
        'produits' => array_slice($produitsWithRatings, 0, 10), // Limite à 10 produits récents
        'categories' => $this->categorieRepository->findAll(),
    ]);
}    #[Route('/home-categorie/{id}', name: 'app_home_category')]
    public function category(int $id, Request $request): Response
    {
        $category = $this->categorieRepository->findOneById($id);
        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $produits = $this->produitsRepository->findByCategoryName($category->getName());
        $produitsWithRatings = $this->addRatingsToProducts($produits);
        $categories = $this->categorieRepository->findAll();
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);

        return $this->render('home/index.html.twig', [
            'produits' => $produitsWithRatings,
            'selectedCategory' => $category->getName(),
            'categories' => $categories,
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

    private function addRatingsToProducts(array $produits): array
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

    #[Route('/search', name: 'search_products')]
public function searchProducts(Request $request): Response
{
    $query = $request->query->get('query');
    $categoryId = $request->query->get('category');

    $produits = $this->produitsRepository->searchByNameAndCategory($query, $categoryId);
    $produitsWithRatings = $this->addRatingsToProducts($produits);

    $categories = $this->categorieRepository->findAll();
    $panier = $this->getPanier();
    $wishlistCount = $this->getWishlistCount($request);

    return $this->render('home/search_results.html.twig', [
        'produits' => $produitsWithRatings,
        'query' => $query,
        'selectedCategory' => $categoryId ? $this->categorieRepository->find($categoryId)->getName() : null,
        'categories' => $categories,
        'panier' => $panier,
        'wishlistCount' => $wishlistCount,
    ]);
}
}