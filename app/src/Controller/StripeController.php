<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
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
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        // Logique pour traiter le paiement réussi
        $this->addFlash('success', 'Paiement réussi ! Votre commande a été traitée.');
        
        // Redirection vers la page d'accueil
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
        // Utilisez la même logique que dans votre CheckoutController
        if ($user = $this->getUser()) {
            return method_exists($user, 'getPaniers') ? $user->getPaniers()->last() : null;
        }
        return null;
    }
}