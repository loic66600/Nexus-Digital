<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;

class CategoriesController extends AbstractController
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        // Récupérer toutes les catégories avec leurs produits
        $categories = $this->categorieRepository->findAllWithProducts();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}