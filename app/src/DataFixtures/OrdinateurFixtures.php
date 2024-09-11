<?php

namespace App\DataFixtures;

use App\Entity\Produits;
use App\Entity\Categorie;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdinateurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des catégories
        $categoriesData = [
            'Ordinateurs Portables' => 'Catégorie pour les ordinateurs portables',
            'Smartphones' => 'Catégorie pour les smartphones',
            'Appareils Photo' => 'Catégorie pour les appareils photo',
            'Accessoires' => 'Catégorie pour les accessoires électroniques',
        ];

        $categories = [];

        foreach ($categoriesData as $name => $description) {
            $category = new Categorie();
            $category->setName($name);
            $category->setDescription($description);
            $manager->persist($category);
            $categories[$name] = $category;
        }

        // Créer des produits et associer le stock
        $productsData = [
            // Ordinateurs Portables
            ['name' => 'Apple MacBook Pro 14', 'description' => 'Ordinateur portable haut de gamme.', 'prices' => 2499.99, 'category' => 'Ordinateurs Portables', 'quantity' => 20],
            ['name' => 'Dell XPS 13', 'description' => 'Ordinateur portable ultra-performant.', 'prices' => 1499.99, 'category' => 'Ordinateurs Portables', 'quantity' => 15],
            ['name' => 'HP Spectre x360', 'description' => 'Ordinateur convertible élégant.', 'prices' => 1799.99, 'category' => 'Ordinateurs Portables', 'quantity' => 10],
            ['name' => 'Lenovo ThinkPad X1 Carbon', 'description' => 'Durabilité et performance.', 'prices' => 2299.99, 'category' => 'Ordinateurs Portables', 'quantity' => 12],
            ['name' => 'Asus ROG Zephyrus G14', 'description' => 'Gaming puissant et portable.', 'prices' => 1999.99, 'category' => 'Ordinateurs Portables', 'quantity' => 8],
            ['name' => 'Microsoft Surface Laptop 4', 'description' => 'Polyvalent et élégant.', 'prices' => 1499.99, 'category' => 'Ordinateurs Portables', 'quantity' => 25],

            // Smartphones
            ['name' => 'iPhone 14 Pro', 'description' => 'Dernier smartphone par Apple.', 'prices' => 999.99, 'category' => 'Smartphones', 'quantity' => 30],
            ['name' => 'Samsung Galaxy S23', 'description' => 'Smartphone de pointe.', 'prices' => 1199.99, 'category' => 'Smartphones', 'quantity' => 20],
            ['name' => 'Google Pixel 7', 'description' => 'Smartphone par Google.', 'prices' => 899.99, 'category' => 'Smartphones', 'quantity' => 18],
            ['name' => 'OnePlus 10 Pro', 'description' => 'Performance et design.', 'prices' => 799.99, 'category' => 'Smartphones', 'quantity' => 22],
            ['name' => 'Xiaomi Mi 11', 'description' => 'Technologie avancée.', 'prices' => 699.99, 'category' => 'Smartphones', 'quantity' => 5],
            ['name' => 'Huawei P50 Pro', 'description' => 'Photographie exceptionnelle.', 'prices' => 1099.99, 'category' => 'Smartphones', 'quantity' => 15],

            // Appareils Photo
            ['name' => 'Canon EOS R5', 'description' => 'Appareil photo sans miroir.', 'prices' => 3899.99, 'category' => 'Appareils Photo', 'quantity' => 7],
            ['name' => 'Sony A7 IV', 'description' => 'Appareil photo plein format.', 'prices' => 2499.99, 'category' => 'Appareils Photo', 'quantity' => 10],
            ['name' => 'Nikon Z6 II', 'description' => 'Performance et qualité.', 'prices' => 1999.99, 'category' => 'Appareils Photo', 'quantity' => 5],
            ['name' => 'Fujifilm X-T4', 'description' => 'Appareil photo hybride.', 'prices' => 1699.99, 'category' => 'Appareils Photo', 'quantity' => 8],
            ['name' => 'Panasonic Lumix S5', 'description' => 'Compact et puissant.', 'prices' => 1799.99, 'category' => 'Appareils Photo', 'quantity' => 0],
            ['name' => 'Olympus OM-D E-M1 Mark III', 'description' => 'Stabilité et rapidité.', 'prices' => 1499.99, 'category' => 'Appareils Photo', 'quantity' => 2],

            // Accessoires
            ['name' => 'Casque Audio Bose', 'description' => 'Casque audio sans fil.', 'prices' => 299.99, 'category' => 'Accessoires', 'quantity' => 40],
            ['name' => 'Enceinte Bluetooth JBL', 'description' => 'Son puissant et clair.', 'prices' => 129.99, 'category' => 'Accessoires', 'quantity' => 35],
            ['name' => 'Écouteurs AirPods Pro', 'description' => 'Écouteurs sans fil.', 'prices' => 249.99, 'category' => 'Accessoires', 'quantity' => 50],
            ['name' => 'Chargeur Sans Fil Anker', 'description' => 'Charge rapide et efficace.', 'prices' => 49.99, 'category' => 'Accessoires', 'quantity' => 100],
            ['name' => 'Souris Logitech MX Master 3', 'description' => 'Souris ergonomique.', 'prices' => 99.99, 'category' => 'Accessoires', 'quantity' => 75],
            ['name' => 'Clavier Mécanique Corsair', 'description' => 'Clavier de jeu.', 'prices' => 149.99, 'category' => 'Accessoires', 'quantity' => 60],
        ];

        foreach ($productsData as $productData) {
            $product = new Produits();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrices($productData['prices']);
            $product->setActive(true);

            // Associer le produit à la catégorie appropriée
            $product->addCategory($categories[$productData['category']]);

            $manager->persist($product);

            // Créer le stock associé
            $stock = new Stock();
            $stock->setQuantity($productData['quantity']);
            $stock->setProduct($product);

            $manager->persist($stock);
        }

        $manager->flush();
    }
}