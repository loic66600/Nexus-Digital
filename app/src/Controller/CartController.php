<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\LignePanier;
use App\Entity\Panier;
use App\Entity\Status;
use App\Entity\Avis;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $panier = null;
        if ($this->getUser()) {
            $panier = $this->getUser()->getPaniers()->last();
        }

        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        
        if (!empty($wishlistIds)) {
            $wishlistProducts = $entityManager->getRepository(Produits::class)->findBy(['id' => $wishlistIds]);
            $wishlistWithRatings = $this->addRatingsToProducts($wishlistProducts);
        } else {
            $wishlistWithRatings = [];
        }

        return $this->render('cart/index.html.twig', [
            'panier' => $panier,
            'wishlist' => $wishlistWithRatings,
            'wishlistCount' => count($wishlistIds),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Produits $produit, EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

            $defaultStatus = $entityManager->getRepository(Status::class)->findOneBy(['label' => 'Pending']);
            if (!$defaultStatus) {
                throw new \Exception('Default status not found.');
            }
            $panier->setStatus($defaultStatus);

            $entityManager->persist($panier);
        }

        $lignePanier = null;
        foreach ($panier->getLignePaniers() as $ligne) {
            if ($ligne->getProduct()->getId() === $produit->getId()) {
                $lignePanier = $ligne;
                break;
            }
        }

        if ($lignePanier) {
            $lignePanier->setQuantity($lignePanier->getQuantity() + 1);
        } else {
            $lignePanier = new LignePanier();
            $lignePanier->setProduct($produit);
            $lignePanier->setQuantity(1);
            $lignePanier->setPanier($panier);
            $entityManager->persist($lignePanier);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre article a été ajouté au panier.');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(LignePanier $lignePanier, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($lignePanier) {
            $entityManager->remove($lignePanier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/wishlist/add/{id}', name: 'wishlist_add', methods: ['POST'])]
    public function addWishlist(Produits $produit, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $wishlist = $session->get('wishlist', []);

        if (!in_array($produit->getId(), $wishlist)) {
            $wishlist[] = $produit->getId();
            $session->set('wishlist', $wishlist);
            $this->addFlash('success', 'Produit ajouté à votre liste de souhaits.');
        } else {
            $this->addFlash('info', 'Produit déjà dans votre liste de souhaits.');
        }

        return $this->redirect($request->headers->get('referer'));
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

    private function addRatingsToProducts(array $products): array
    {
        $productsWithRatings = [];
        foreach ($products as $product) {
            $productsWithRatings[] = [
                'produit' => $product,
                'averageRating' => $this->calculateAverageRating($product)
            ];
        }
        return $productsWithRatings;
    }
}