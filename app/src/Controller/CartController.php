<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\LignePanier;
use App\Entity\Panier;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer le panier de l'utilisateur actuel
        $panier = null;
        if ($this->getUser()) {
            $panier = $this->getUser()->getPaniers()->last();
        }

        // Récupérer la wishlist à partir de la session
        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        
        if (!empty($wishlistIds)) {
            $wishlistProducts = $entityManager->getRepository(Produits::class)->findBy(['id' => $wishlistIds]);
        } else {
            $wishlistProducts = [];
        }

        return $this->render('cart/index.html.twig', [
            'panier' => $panier,
            'wishlist' => $wishlistProducts,
            'wishlistCount' => count($wishlistIds),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Produits $produit, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer ou créer un nouveau panier pour l'utilisateur actuel
        $user = $this->getUser();
        $panier = null;
        
        if ($user && $user->getPaniers()) {
            $panier = $user->getPaniers()->last();
        }

        if (!$panier) {
            $panier = new Panier();
            $panier->setClient($user);
            $panier->setNumCommande(uniqid('cmd_', true));
            $panier->setOrderDate(new \DateTime());

            // Vérifier et définir le statut par défaut
            $defaultStatus = $entityManager->getRepository(Status::class)->findOneBy(['label' => 'Pending']);
            if (!$defaultStatus) {
                throw new \Exception('Default status not found.');
            }
            $panier->setStatus($defaultStatus);

            $entityManager->persist($panier);
        }

        // Vérifier si le produit est déjà dans le panier
        $lignePanier = null;
        foreach ($panier->getLignePaniers() as $ligne) {
            if ($ligne->getProduct()->getId() === $produit->getId()) {
                $lignePanier = $ligne;
                break;
            }
        }

        if ($lignePanier) {
            // Augmenter la quantité si le produit est déjà dans le panier
            $lignePanier->setQuantity($lignePanier->getQuantity() + 1);
        } else {
            // Créer une nouvelle ligne de panier si le produit n'est pas encore dans le panier
            $lignePanier = new LignePanier();
            $lignePanier->setProduct($produit);
            $lignePanier->setQuantity(1);
            $lignePanier->setPanier($panier);
            $entityManager->persist($lignePanier);
        }

        // Persister les changements
        $entityManager->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Votre article a été ajouté au panier.');

        // Rediriger vers la page précédente
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(LignePanier $lignePanier, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Supprimer la ligne de panier
        if ($lignePanier) {
            $entityManager->remove($lignePanier);
            $entityManager->flush();
        }

        // Rediriger vers la page du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/wishlist/add/{id}', name: 'wishlist_add', methods: ['POST'])]
    public function addWishlist(Produits $produit, Request $request): Response
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
}