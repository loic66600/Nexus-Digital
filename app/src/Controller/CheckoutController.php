<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produits;
use App\Entity\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

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

                $produits[] = [
                    'nom' => $produit->getName(),
                    'quantite' => $quantite,
                    'prix' => $prix,
                    'montant' => $montant,
                ];

                $montantTotal += $montant;
            }
        }

        // Récupérer le nombre d'articles dans la wishlist
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        $wishlistCount = count($wishlistIds);

        // Récupérer les produits de la wishlist
        $wishlistProducts = !empty($wishlistIds) 
            ? $entityManager->getRepository(Produits::class)->findBy(['id' => $wishlistIds])
            : [];

        // Récupérer l'adresse principale de l'utilisateur
        $userAddress = $user->getUserAdresse()->first();

        return $this->render('checkout/index.html.twig', [
            'user' => $user,
            'userAddress' => $userAddress,
            'produits' => $produits,
            'montantTotal' => $montantTotal,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
            'wishlistProducts' => $wishlistProducts,
        ]);
    }

    private function getPanier()
    {
        if ($user = $this->getUser()) {
            return $user->getPaniers()->last();
        }
        return null;
    }
    #[Route('/add-shipping-address', name: 'app_add_shipping_address', methods: ['POST'])]
    public function addShippingAddress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $newAddress = new UserInfo();
        $newAddress->setUser($user);
        $newAddress->setAddressName('Adresse de livraison');
        $newAddress->setAddress($request->request->get('shipping_address'));
        $newAddress->setCity($request->request->get('shipping_city'));
        $newAddress->setCountry($request->request->get('shipping_country'));
        $newAddress->setZipCode($request->request->get('shipping_zip_code'));

        $entityManager->persist($newAddress);
        $entityManager->flush();

        $this->addFlash('success', 'Nouvelle adresse de livraison ajoutée avec succès.');

        return $this->redirectToRoute('app_checkout');
    }
}