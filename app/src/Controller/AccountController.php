<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Form\UserType;
use App\Entity\Categorie;
use App\Form\UserinfoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();
        $panier = $this->getPanier();

        // Formulaire pour les informations utilisateur
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Vérifiez si un nouveau mot de passe a été saisi
            if ($plainPassword = $userForm->get('plainPassword')->getData()) {
                // Hachez le nouveau mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
        
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
            return $this->redirectToRoute('app_account');
        }
        // Initialisation des variables de formulaire
        $addressForm = null;
        $editAddressForm = null;
        $editAddress = null;

        // Vérifiez si une adresse doit être modifiée
        if ($request->query->has('edit_address_id')) {
            // Modification d'une adresse existante
            $addressId = (int) $request->query->get('edit_address_id');
            /** @var UserInfo|null $address */
            $address = $entityManager->getRepository(UserInfo::class)->find($addressId);

            if ($address && $address->getUser() === $user) {
                // Utiliser l'adresse existante pour créer le formulaire de modification
                $editAddressForm = $this->createForm(UserinfoType::class, $address);
                $editAddressForm->handleRequest($request);

                if ($editAddressForm->isSubmitted() && $editAddressForm->isValid()) {
                    // Enregistrer les modifications
                    $entityManager->flush();
                    $this->addFlash('success', 'L\'adresse a été modifiée avec succès.');
                    return $this->redirectToRoute('app_account');
                }
                // Indiquer qu'une adresse est en cours d'édition
                $editAddress = $address;
            }
        } else {
            // Ajouter une nouvelle adresse
            $newAddress = new UserInfo();
            $newAddress->setUser($user);
            $addressForm = $this->createForm(UserinfoType::class, $newAddress);
            $addressForm->handleRequest($request);

            if ($addressForm->isSubmitted() && $addressForm->isValid()) {
                // Enregistrer la nouvelle adresse
                $entityManager->persist($newAddress);
                $entityManager->flush();
                $this->addFlash('success', 'La nouvelle adresse a été ajoutée avec succès.');
                return $this->redirectToRoute('app_account');
            }
        }
         // Récupérer les catégories
         $categories = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'panier' => $panier,
            'wishlistCount' => count($request->getSession()->get('wishlist', [])),
            'userForm' => $userForm->createView(),
            'addressForm' => isset($addressForm) ? $addressForm->createView() : null,
            'editAddressForm' => isset($editAddressForm) ? $editAddressForm->createView() : null,
            'editAddress' => isset($editAddress) ? $editAddress : null,
            'menuCategories' => $categories,
        ]);
    }

    #[Route('/account/address/{id}/delete', name: 'app_account_address_delete', methods: ['POST'])]
    public function deleteAddress(Request $request, EntityManagerInterface $entityManager, UserInfo $address): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), (string) $request->request->get('_token')) &&
            ($address && $address->getUser() === $this->getUser())) {

            // Supprimer l'adresse
            $entityManager->remove($address);
            $entityManager->flush();
            $this->addFlash('success', 'L\'adresse a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_account');
    }

    private function getPanier()
    {
        if ($user = $this->getUser()) {
            return !empty($paniers = $user->getPaniers()->toArray()) ? end($paniers) : null;
        }
        
        return null;
    }
}