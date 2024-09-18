<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    private $produitsRepository;

    public function __construct(ProduitsRepository $produitsRepository)
    {
        $this->produitsRepository = $produitsRepository;
    }



    #[Route('/', name: 'home_index')]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        $produits = $produitsRepository->AllProduit();

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }

}

