<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $panier = $this->getPanier();

        $session = $request->getSession();
        $wishlistIds = $session->get('wishlist', []);
        $wishlistCount = count($wishlistIds);

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'panier' => $panier,
            'wishlistCount' => $wishlistCount,
        ]);
    }

    #[Route('/account/edit', name: 'app_account_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/address/{id}/edit', name: 'app_account_address_edit')]
    public function editAddress(Request $request, EntityManagerInterface $entityManager, UserInfo $address): Response
    {
        if ($address->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette adresse.');
        }

        if ($request->isMethod('POST')) {
            // Mettre à jour les informations de l'adresse
            $address->setAddressName($request->request->get('addressName'));
            $address->setAddress($request->request->get('address'));
            $address->setCity($request->request->get('city'));
            $address->setZipCode($request->request->get('zipCode'));
            $address->setCountry($request->request->get('country'));

            // Enregistrer les modifications
            $entityManager->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit_address.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/account/address/{id}/delete', name: 'app_account_address_delete')]
    public function deleteAddress(EntityManagerInterface $entityManager, UserInfo $address): Response
    {
        if ($address && $address->getUser() === $this->getUser()) {
            // Supprimer l'adresse
            $entityManager->remove($address);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_account');
    }

    private function getPanier()
    {
        if ($user = $this->getUser()) {
            return method_exists($user, 'getPaniers') ? $user->getPaniers()->last() : null;
        }
        return null;
    }
}