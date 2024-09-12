<?php

namespace App\DataFixtures;

use App\Entity\Produits;
use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Chemin vers le dossier des images
        $imagePath = 'assets/img/';

        // Liste des produits et leurs images associées
        $productsData = [
            'Apple MacBook Pro 14' => ['Apple MacBook Pro 14.jpg', 'Apple MacBook Pro 14-1.jpg', 'Apple MacBook Pro 14-2.jpg'],
            'Dell XPS 13' => ['Dell XPS 13.jpg', 'Dell XPS 13.1.jpg'],
            'HP Spectre x360' => ['HP Spectre x360.jpg', 'HP Spectre x360.1.jpg', 'HP Spectre x360.2.jpg'],
            'Lenovo ThinkPad X1 Carbon' => ['Lenovo ThinkPad X1 Carbon.1.jpg', 'Lenovo ThinkPad X1 Carbon.2.jpg', 'Lenovo ThinkPad X1 Carbon.jpg'],
            'Asus ROG Zephyrus G14' => ['Asus ROG Zephyrus G14.jpg', 'Asus ROG Zephyrus G14.1.jpg', 'Asus ROG Zephyrus G14.2.jpg'],
            'Microsoft Surface Laptop 4' => ['Microsoft Surface Laptop 4.jpg', 'Microsoft Surface Laptop 4.1.jpg', 'Microsoft Surface Laptop 4.2.jpg'],
            'iPhone 14 Pro' => ['iPhone 14 Pro.webp', 'iPhone 14 Pro.1.webp', 'iPhone 14 Pro.2.webp'],
            'Samsung Galaxy S23' => ['Samsung Galaxy S23.1.jpg', 'Samsung Galaxy S23.2.jpg', 'Samsung Galaxy S23.jpg'],
            'Google Pixel 7' => ['Google Pixel 7.jpg', 'Google Pixel 7.1.jpg', 'Google Pixel 7.2.jpg'],
            'OnePlus 10 Pro' => ['OnePlus 10 Pro.jpg', 'OnePlus 10 Pro.1.jpg', 'OnePlus 10 Pro.2.jpg'],
            'Xiaomi Mi 11' => ['Xiaomi Mi 11.jpg', 'Xiaomi Mi 11.1.jpg', 'Xiaomi Mi 11.2.jpg'],
            'Huawei P50 Pro' => ['Huawei P50 Pro.jpg', 'Huawei P50 Pro.1.jpg', 'Huawei P50 Pro.2.jpg'],
            'Canon EOS R5' => ['Canon EOS R5.jpg', 'Canon EOS R5.1.jpg', 'Canon EOS R5.2.jpg'],
            'Sony A7 IV' => ['Sony A7 IV.1.jpg', 'Sony A7 IV.2.jpg', 'Sony A7 IV.jpg'],
            'Nikon Z6 II' => ['Nikon Z6 II.jpg', 'Nikon Z6 II.1.jpg', 'Nikon Z6 II.2.jpg'],
            'Fujifilm X-T4' => ['Fujifilm X-T4.jpg', 'Fujifilm X-T4.1.jpg', 'Fujifilm X-T4.2.jpg'],
            'Panasonic Lumix S5' => ['Panasonic Lumix S5.1.jpg', 'Panasonic Lumix S5.jpg'],
            'Olympus OM-D E-M1 Mark III' => ['Olympus OM-D E-M1 Mark III.jpg', 'Olympus OM-D E-M1 Mark III.1.jpg', 'Olympus OM-D E-M1 Mark III.2.jpg'],
            'Casque Audio Bose' => ['Casque Audio Bose.jpg', 'Casque Audio Bose.1.jpg', 'Casque Audio Bose.2.jpg'],
            'Enceinte Bluetooth JBL' => ['Enceinte Bluetooth JBL.jpg', 'Enceinte Bluetooth JBL.1.jpg', 'Enceinte Bluetooth JBL.2.jpg'],
            'Écouteurs AirPods Pro' => ['Écouteurs AirPods Pro.jpg', 'Écouteurs AirPods Pro.1.jpg', 'Écouteurs AirPods Pro.2.jpg'],
            'Chargeur Sans Fil Anker' => ['Chargeur Sans Fil Anker.jpg', 'Chargeur Sans Fil Anker.1.jpg', 'Chargeur Sans Fil Anker.2.jpg'],
            'Souris Logitech MX Master 3' => ['Souris Logitech MX Master 3.jpg', 'Souris Logitech MX Master 3.1.jpg', 'Souris Logitech MX Master 3.2.jpg'],
            'Clavier Mécanique Corsair' => ['Clavier Mécanique Corsair.jpg', 'Clavier Mécanique Corsair.1.jpg']
        ];

        foreach ($productsData as $productName => $imageNames) {
            $product = $manager->getRepository(Produits::class)->findOneBy(['name' => $productName]);

            if ($product) {
                foreach ($imageNames as $imageName) {
                    $image = new Images();
                    $image->setName($imagePath . $imageName);
                    $image->setProduct($product);

                    $manager->persist($image);
                }
            }
        }

        $manager->flush();
    }
}