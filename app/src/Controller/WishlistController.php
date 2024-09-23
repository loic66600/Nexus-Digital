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
    public function add(Produits $produit, Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Ajouter le produit à la session de wishlist
        $session = $request->getSession();
        $wishlist = $session->get('wishlist', []);

        if (!in_array($produit->getId(), $wishlist)) {
            $wishlist[] = $produit->getId();
            $session->set('wishlist', $wishlist);
            $this->addFlash('success', 'Produit ajouté à votre liste de souhaits.');
        } else {
            $this->addFlash('info', 'Produit déjà dans votre liste de souhaits.');
        }

        // Rediriger vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/wishlist', name: 'wishlist_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer les produits de la wishlist à partir de la session
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        
        if (!empty($wishlistIds)) {
            $wishlistProducts = $entityManager->getRepository(Produits::class)->findBy(['id' => $wishlistIds]);
        } else {
            $wishlistProducts = [];
        }

        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlistProducts,
        ]);
    }
}