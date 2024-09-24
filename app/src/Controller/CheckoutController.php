<?php

namespace App\Controller;

use App\Entity\Produits; // Assurez-vous d'importer l'entité Produits avec le bon namespace
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        // Calculer le montant total et préparer les détails des produits
        $montantTotal = 0;
        $produits = [];

        if ($panier) {
            foreach ($panier->getLignePaniers() as $lignePanier) {
                $produit = $lignePanier->getProduct();
                $quantite = $lignePanier->getQuantity();
                $prix = $produit->getPrices();
                $montant = $quantite * $prix;

                // Ajouter les détails du produit à la liste
                $produits[] = [
                    'nom' => $produit->getName(),
                    'quantite' => $quantite,
                    'prix' => $prix,
                    'montant' => $montant,
                ];

                // Ajouter au montant total
                $montantTotal += $montant;
            }
        }

        // Récupérer le nombre d'articles dans la wishlist à partir de la session
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        $wishlistCount = count($wishlistIds);

        // Récupérer les produits de la wishlist
        if (!empty($wishlistIds)) {
            $wishlistProducts = $entityManager->getRepository(Produits::class)->findBy(['id' => $wishlistIds]);
        } else {
            $wishlistProducts = [];
        }

        return $this->render('checkout/index.html.twig', [
            'produits' => $produits,
            'montantTotal' => $montantTotal,
            'panier' => $panier, // Passer le panier au template
            'wishlistCount' => $wishlistCount, // Passer le nombre d'articles dans la wishlist
            'wishlistProducts' => $wishlistProducts, // Passer les produits de la wishlist
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