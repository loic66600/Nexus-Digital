<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer l'utilisateur actuel
        $user = $this->getUser();

        // Récupérer le panier de l'utilisateur actuel
        $panier = $this->getPanier();

        // Récupérer le nombre d'articles dans la wishlist à partir de la session
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        $wishlistCount = count($wishlistIds);

        // Rendre la vue avec les informations de l'utilisateur, le panier et le nombre d'articles dans la wishlist
        return $this->render('account/index.html.twig', [
            'user' => $user,
            'userAddresses' => $user->getUserAdresse(),
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
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