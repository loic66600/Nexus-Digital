<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Stock;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session')]
    public function createCheckoutSession(Request $request): Response
    {
        $panier = $this->getPanier();
        
        if (!$panier || $panier->getLignePaniers()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_checkout');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];
        foreach ($panier->getLignePaniers() as $lignePanier) {
            $produit = $lignePanier->getProduct();
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $produit->getName(),
                    ],
                    'unit_amount' => $produit->getPrices() * 100, // Stripe utilise les centimes
                ],
                'quantity' => $lignePanier->getQuantity(),
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', ['panier_id' => $panier->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success/{panier_id}', name: 'payment_success')]
    public function paymentSuccess(int $panier_id): Response
    {
        $panier = $this->entityManager->getRepository(Panier::class)->find($panier_id);
    
        if (!$panier) {
            throw $this->createNotFoundException('Panier non trouvé');
        }
    
        foreach ($panier->getLignePaniers() as $lignePanier) {
            $produit = $lignePanier->getProduct();
            $quantiteVendue = $lignePanier->getQuantity();
    
            // Mise à jour du stock
            $stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['product' => $produit]);
            if ($stock) {
                $nouvelleQuantite = max(0, $stock->getQuantity() - $quantiteVendue);
                $stock->setQuantity($nouvelleQuantite);
                $this->entityManager->persist($stock);
            }
    
            // Supprimer la ligne de panier
            $this->entityManager->remove($lignePanier);
        }
    
        // Maintenant, supprimer le panier
        $this->entityManager->remove($panier);
        $this->entityManager->flush();
    
        $this->addFlash('success', 'Paiement réussi ! Votre commande a été traitée.');
        return $this->redirectToRoute('home_index');
    }
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('app_checkout');
    }

    private function getPanier()
    {
        if ($user = $this->getUser()) {
            return $user->getPaniers()->last();
        }
        return null;
    }
}