<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\UserInfo;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Persister l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/address/{addressId}/edit', name: 'app_user_address_edit', methods: ['GET', 'POST'])]
    public function editAddress(Request $request, EntityManagerInterface $entityManager, User $user, int $addressId): Response
    {
        $address = $entityManager->getRepository(UserInfo::class)->find($addressId);

        if (!$address || $address->getUser() !== $user) {
            throw $this->createNotFoundException('Adresse non trouvée ou non autorisée.');
        }

        if ($request->isMethod('POST')) {
            $address->setAddressName($request->request->get('addressName'));
            $address->setAddress($request->request->get('address'));
            $address->setCity($request->request->get('city'));
            $address->setZipCode($request->request->get('zipCode'));
            $address->setCountry($request->request->get('country'));

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        return $this->render('account/edit_address.html.twig', [
            'user' => $user,
            'address' => $address,
        ]);
    }

    #[Route('/{id}/address/{addressId}/delete', name: 'app_user_address_delete', methods: ['POST'])]
    public function deleteAddress(Request $request, EntityManagerInterface $entityManager, User $user, int $addressId): Response
    {
        $address = $entityManager->getRepository(UserInfo::class)->find($addressId);

        if ($address && $this->isCsrfTokenValid('delete'.$addressId, $request->request->get('_token'))) {
            if ($address->getUser() === $user) {
                $entityManager->remove($address);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }}

