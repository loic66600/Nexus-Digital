<?php

namespace App\Controller;

use App\Entity\Produits;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractController
{
    #[Route('/wishlist/add/{id}', name: 'wishlist_add', methods: ['POST'])]
    public function add(Produits $produit, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Logique pour ajouter le produit à la wishlist de l'utilisateur
        $user = $this->getUser();
        if (method_exists($user, 'getWishlist')) {
            $wishlist = $user->getWishlist();
            if (!$wishlist->contains($produit)) {
                $wishlist->add($produit);
                $entityManager->flush();
                $this->addFlash('success', 'Produit ajouté à votre liste de souhaits.');
            } else {
                $this->addFlash('info', 'Produit déjà dans votre liste de souhaits.');
            }
        }

        // Rediriger vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/wishlist', name: 'wishlist_index', methods: ['GET'])]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer la wishlist de l'utilisateur actuel
        $wishlist = null;
        if ($this->getUser() && method_exists($this->getUser(), 'getWishlist')) {
            $wishlist = $this->getUser()->getWishlist();
        }

        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
}