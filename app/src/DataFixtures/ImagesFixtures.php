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
            'Apple MacBook Pro 14' => 'macbook_pro.jpg',
            'Dell XPS 13' => 'dell_xps.jpg',
            'HP Spectre x360' => 'hp_spectre.jpg',
            'Lenovo ThinkPad X1 Carbon' => 'thinkpad_x1.jpg',
            'Asus ROG Zephyrus G14' => 'asus_rog.jpg',
            'Microsoft Surface Laptop 4' => 'surface_laptop.jpg',
            'iPhone 14 Pro' => 'iphone_14_pro.jpg',
            'Samsung Galaxy S23' => 'samsung_galaxy_s23.jpg',
            'Google Pixel 7' => 'google_pixel_7.jpg',
            'OnePlus 10 Pro' => 'oneplus_10_pro.jpg',
            'Xiaomi Mi 11' => 'xiaomi_mi_11.jpg',
            'Huawei P50 Pro' => 'huawei_p50_pro.jpg',
            'Canon EOS R5' => 'canon_eos_r5.jpg',
            'Sony A7 IV' => 'sony_a7_iv.jpg',
            'Nikon Z6 II' => 'nikon_z6_ii.jpg',
            'Fujifilm X-T4' => 'fujifilm_x_t4.jpg',
            'Panasonic Lumix S5' => 'panasonic_lumix_s5.jpg',
            'Olympus OM-D E-M1 Mark III' => 'olympus_om_d_e_m1_mark_iii.jpg',
            'Casque Audio Bose' => 'bose_headphones.jpg',
            'Enceinte Bluetooth JBL' => 'jbl_speaker.jpg',
            'Écouteurs AirPods Pro' => 'airpods_pro.jpg',
            'Chargeur Sans Fil Anker' => 'anker_wireless_charger.jpg',
            'Souris Logitech MX Master 3' => 'logitech_mouse.jpg',
            'Clavier Mécanique Corsair' => 'corsair_keyboard.jpg',
        ];

        foreach ($productsData as $productName => $imageName) {
            // Rechercher le produit existant
            $product = $manager->getRepository(Produits::class)->findOneBy(['name' => $productName]);

            if ($product) {
                // Créer l'image associée
                $image = new Images();
                $image->setImage($imagePath . $imageName);
                $image->setProduct($product);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}