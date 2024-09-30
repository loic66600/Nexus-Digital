<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    public function getFullCart()
    {
        $cart = $this->getCart();
        $fullCart = [];

        foreach ($cart as $id => $quantity) {
            $produit = $this->produitRepository->find($id);
            if ($produit) {
                $fullCart[] = [
                    'produit' => $produit,
                    'quantite' => $quantity,
                ];
            }
        }

        return $fullCart;
    }

    public function clearCart()
    {
        $this->requestStack->getSession()->remove('cart');
    }

    // Ajoutez d'autres méthodes pour gérer le panier si nécessaire
}