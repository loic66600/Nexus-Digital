<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
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

        return $this->render('checkout/index.html.twig', [
            'produits' => $produits,
            'montantTotal' => $montantTotal,
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