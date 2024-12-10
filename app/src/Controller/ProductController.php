<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ProductController extends AbstractController
{
    private $produitsRepository;
    private $security;
    private $entityManager;

    public function __construct(ProduitsRepository $produitsRepository, Security $security, EntityManagerInterface $entityManager)
    {
        $this->produitsRepository = $produitsRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(int $id, Request $request): Response
    {
        $productDetails = $this->produitsRepository->findProductDetails($id);

        if (!$productDetails) {
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'id ' . $id);
        }

        $associatedProducts = $this->getAssociatedProducts($productDetails, $id);
        $totalStockQuantity = $this->calculateTotalStockQuantity($productDetails);
        $averageRating = $this->calculateAverageRating($productDetails);
        $panier = $this->getPanier();
        $wishlistCount = $this->getWishlistCount($request);

        // Calculate average rating for each associated product
        $associatedProductsWithRatings = [];
        foreach ($associatedProducts as $product) {
            $associatedProductsWithRatings[] = [
                'product' => $product,
                'averageRating' => $this->calculateAverageRating($product)
            ];
        }
          // Récupérer les catégories
          $categories = $this->entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('product/index.html.twig', [
            'product' => $productDetails,
            'averageRating' => $averageRating,
            'totalStockQuantity' => $totalStockQuantity,
            'associatedProducts' => $associatedProductsWithRatings,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
            'menuCategories' => $categories,
        ]);
    }

    #[Route('/product/{id}/submit-review', name: 'submit_review', methods: ['POST'])]
    public function submitReview(int $id, Request $request): Response
    {
        $product = $this->produitsRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
    
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->addFlash('error', 'Vous devez être connecté pour laisser un avis.');
            return $this->redirectToRoute('product_show', ['id' => $id]);
        }
    
        $note = $request->request->getInt('_rating');
        $comment = $request->request->get('_comment');
    
        $avis = new Avis();
        $avis->setNote($note);
        $avis->setComment($comment);
        $avis->setDateNotice(new \DateTime());
        $avis->setValide(false);
        $avis->setProduct($product);
        $avis->setClient($user);
    
        $this->entityManager->persist($avis);
        $this->entityManager->flush();
    
        $this->addFlash('success', 'Votre avis a été soumis et sera publié après validation.');
    
        return $this->redirectToRoute('product_show', ['id' => $id]);
    }

    private function getAssociatedProducts($productDetails, $currentProductId): array
    {
        $associatedProducts = [];
        if ($productDetails->getCategories()->count() > 0) {
            $categoryName = $productDetails->getCategories()->first()->getName();
            $associatedProducts = $this->produitsRepository->findByCategoryName($categoryName);
            $associatedProducts = array_filter($associatedProducts, function ($product) use ($currentProductId) {
                return $product->getId() !== $currentProductId;
            });
        }
        return $associatedProducts;
    }

    private function calculateTotalStockQuantity($productDetails): int
    {
        return array_reduce($productDetails->getStocks()->toArray(), function ($carry, $stock) {
            return $carry + $stock->getQuantity();
        }, 0);
    }

    private function calculateAverageRating($productDetails): float
    {
        $avis = $productDetails->getAvis()->filter(function($avi) {
            return $avi->isValide();
        });
        $totalNotes = array_reduce($avis->toArray(), function ($carry, $avi) {
            return $carry + $avi->getNote();
        }, 0);
        return count($avis) > 0 ? round($totalNotes / count($avis), 1) : 0;
    }

    private function getPanier()
    {
        if ($user = $this->security->getUser()) {
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