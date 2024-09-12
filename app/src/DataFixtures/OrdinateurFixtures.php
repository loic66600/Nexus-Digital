<?php

namespace App\DataFixtures;

use App\Entity\Produits;
use App\Entity\Categorie;
use App\Entity\Stock;
use App\Entity\Images;
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

        // Créer des produits, associer le stock et les images
        $productsData = [
            // Ordinateurs Portables
            ['name' => 'HP Spectre x360', 'description' => 'Ordinateur convertible élégant.', 'prices' => 1799.99, 'category' => 'Ordinateurs Portables', 'quantity' => 10, 'images' => ['HP Spectre x360.jpg', 'HP Spectre x360.1.jpg', 'HP Spectre x360.2.jpg']],
            ['name' => 'Lenovo ThinkPad X1 Carbon', 'description' => 'Durabilité et performance.', 'prices' => 2299.99, 'category' => 'Ordinateurs Portables', 'quantity' => 12, 'images' => ['Lenovo ThinkPad X1 Carbon.1.jpg', 'Lenovo ThinkPad X1 Carbon.2.jpg', 'Lenovo ThinkPad X1 Carbon.jpg']],
            ['name' => 'Asus ROG Zephyrus G14', 'description' => 'Gaming puissant et portable.', 'prices' => 1999.99, 'category' => 'Ordinateurs Portables', 'quantity' => 8, 'images' => ['Asus ROG Zephyrus G14.jpg', 'Asus ROG Zephyrus G14.1.jpg', 'Asus ROG Zephyrus G14-2.jpg']],
            ['name' => 'Microsoft Surface Laptop 4', 'description' => 'Polyvalent et élégant.', 'prices' => 1499.99, 'category' => 'Ordinateurs Portables', 'quantity' => 25, 'images' => ['Microsoft Surface Laptop 4.jpg', 'Microsoft Surface Laptop 4.1.jpg', 'Microsoft Surface Laptop 4.2.jpg']],
        
            // Smartphones
            ['name' => 'iPhone 14 Pro', 'description' => 'Dernier smartphone par Apple.', 'prices' => 999.99, 'category' => 'Smartphones', 'quantity' => 30, 'images' => ['iPhone 14 Pro.webp', 'iPhone 14 Pro.1.webp', 'iPhone 14 Pro.2.webp']],
            ['name' => 'Samsung Galaxy S23', 'description' => 'Smartphone de pointe.', 'prices' => 1199.99, 'category' => 'Smartphones', 'quantity' => 20, 'images' => ['Samsung Galaxy S23.1.jpg', 'Samsung Galaxy S23.2.jpg', 'Samsung Galaxy S23.jpg']],
            ['name' => 'Google Pixel 7', 'description' => 'Smartphone par Google.', 'prices' => 899.99, 'category' => 'Smartphones', 'quantity' => 18, 'images' => ['Google Pixel 7.jpg', 'Google Pixel 7.1.jpg', 'Google Pixel 7.2.jpg']],
            ['name' => 'OnePlus 10 Pro', 'description' => 'Performance et design.', 'prices' => 799.99, 'category' => 'Smartphones', 'quantity' => 22, 'images' => ['OnePlus 10 Pro.jpg', 'OnePlus 10 Pro.1.jpg', 'OnePlus 10 Pro.2.jpg']],
            ['name' => 'Xiaomi Mi 11', 'description' => 'Technologie avancée.', 'prices' => 699.99, 'category' => 'Smartphones', 'quantity' => 5, 'images' => ['Xiaomi Mi 11.jpg', 'Xiaomi Mi 11.1.jpg', 'Xiaomi Mi 11.2.jpg']],
            ['name' => 'Huawei P50 Pro', 'description' => 'Photographie exceptionnelle.', 'prices' => 1099.99, 'category' => 'Smartphones', 'quantity' => 15, 'images' => ['Huawei P50 Pro.jpg', 'Huawei P50 Pro.1.jpg', 'Huawei P50 Pro.2.jpg']],
        
            // Appareils Photo
            ['name' => 'Canon EOS R5', 'description' => 'Appareil photo sans miroir.', 'prices' => 3899.99, 'category' => 'Appareils Photo', 'quantity' => 7, 'images' => ['Canon EOS R5.jpg', 'Canon EOS R5.1.jpg', 'Canon EOS R5.2.jpg']],
            ['name' => 'Sony A7 IV', 'description' => 'Appareil photo plein format.', 'prices' => 2499.99, 'category' => 'Appareils Photo', 'quantity' => 10, 'images' => ['Sony A7 IV.1.jpg', 'Sony A7 IV.2.jpg', 'Sony A7 IV.jpg']],
            ['name' => 'Nikon Z6 II', 'description' => 'Performance et qualité.', 'prices' => 1999.99, 'category' => 'Appareils Photo', 'quantity' => 5, 'images' => ['Nikon Z6 II.jpg', 'Nikon Z6 II.1.jpg', 'Nikon Z6 II.2.jpg']],
            ['name' => 'Fujifilm X-T4', 'description' => 'Appareil photo hybride.', 'prices' => 1699.99, 'category' => 'Appareils Photo', 'quantity' => 8, 'images' => ['Fujifilm X-T4.jpg', 'Fujifilm X-T4.1.jpg', 'Fujifilm X-T4.2.jpg']],
            ['name' => 'Panasonic Lumix S5', 'description' => 'Compact et puissant.', 'prices' => 1799.99, 'category' => 'Appareils Photo', 'quantity' => 0, 'images' => ['Panasonic Lumix S5.1.jpg', 'Panasonic Lumix S5.jpg']],
            ['name' => 'Olympus OM-D E-M1 Mark III', 'description' => 'Stabilité et rapidité.', 'prices' => 1499.99, 'category' => 'Appareils Photo', 'quantity' => 2, 'images' => ['Olympus OM-D E-M1 Mark III.jpg', 'Olympus OM-D E-M1 Mark III.1.jpg', 'Olympus OM-D E-M1 Mark III.2.jpg']],
        
            // Accessoires
            ['name' => 'Casque Audio Bose', 'description' => 'Casque audio sans fil.', 'prices' => 299.99, 'category' => 'Accessoires', 'quantity' => 40, 'images' => ['Casque Audio Bose.jpg', 'Casque Audio Bose.1.jpg', 'Casque Audio Bose.2.jpg']],
            ['name' => 'Enceinte Bluetooth JBL', 'description' => 'Son puissant et clair.', 'prices' => 129.99, 'category' => 'Accessoires', 'quantity' => 35, 'images' => ['Enceinte Bluetooth JBL.jpg', 'Enceinte Bluetooth JBL.1.jpg', 'Enceinte Bluetooth JBL.2.jpg']],
            ['name' => 'Écouteurs AirPods Pro', 'description' => 'Écouteurs sans fil.', 'prices' => 249.99, 'category' => 'Accessoires', 'quantity' => 50, 'images' => ['Écouteurs AirPods Pro.jpg', 'Écouteurs AirPods Pro.1.jpg', 'Écouteurs AirPods Pro.2.jpg']],
            ['name' => 'Chargeur Sans Fil Anker', 'description' => 'Charge rapide et efficace.', 'prices' => 49.99, 'category' => 'Accessoires', 'quantity' => 100, 'images' => ['Chargeur Sans Fil Anker.jpg', 'Chargeur Sans Fil Anker.1.jpg', 'Chargeur Sans Fil Anker.2.jpg']],
            ['name' => 'Souris Logitech MX Master 3', 'description' => 'Souris ergonomique.', 'prices' => 99.99, 'category' => 'Accessoires', 'quantity' => 75, 'images' => ['Souris Logitech MX Master 3.jpg', 'Souris Logitech MX Master 3.1.jpg', 'Souris Logitech MX Master 3.2.jpg']],
            ['name' => 'Clavier Mécanique Corsair', 'description' => 'Clavier de jeu.', 'prices' => 149.99, 'category' => 'Accessoires', 'quantity' => 60, 'images' => ['Clavier Mécanique Corsair.jpg', 'Clavier Mécanique Corsair.1.jpg']],
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

            // Associer les images
            foreach ($productData['images'] as $imageName) {
                $image = new Images();
                $image->setName($imageName);
                $image->setProduct($product);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}