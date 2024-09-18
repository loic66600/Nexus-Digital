<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $produitsRepository;
    private $categorieRepository;

    public function __construct(ProduitsRepository $produitsRepository, CategorieRepository $categorieRepository)
    {
        $this->produitsRepository = $produitsRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/{category}', name: 'home_index', defaults: ['category' => null])]
    public function index(?string $category): Response
    {
        if ($category) {
            // Remplacez les tirets bas par des espaces pour correspondre aux noms dans la base de données
            $category = str_replace('_', ' ', $category);
        }
    
        $produits = $this->produitsRepository->findByCategory($category);
        $categories = $this->categorieRepository->findAll();
    
        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'selectedCategory' => $category,
            'categories' => $categories,
        ]);
    }
}