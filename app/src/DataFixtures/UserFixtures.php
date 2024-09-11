<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserInfo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des utilisateurs
        $usersData = [
            [
                'email' => 'user@user.com',
                'password' => 'user',
                'lastName' => 'Koval',
                'firstName' => 'Alona',
                'phone' => '0123456781',
                'roles' => ['ROLE_USER']
            ],
            [
                'email' => 'admin@admin.com',
                'password' => 'admin',
                'lastName' => 'Rossignol',
                'firstName' => 'Loic',
                'phone' => '0123456789',
                'roles' => ['ROLE_ADMIN']
            ]
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $user->setLastName($userData['lastName']);
            $user->setFirstName($userData['firstName']);
            $user->setPhone($userData['phone']);
            $user->setRoles($userData['roles']);

            $manager->persist($user);

            // Créer les informations utilisateur associées
            $userInfo = new UserInfo();
            $userInfo->setAddressName('Maison');
            $userInfo->setAddress('123 rue victoire');
            $userInfo->setCity('Perpignan');
            $userInfo->setCountry('France');
            $userInfo->setZipCode('12345');

            $manager->persist($userInfo);
        }

        $manager->flush();
    }
}