<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $produitsRepository;

    public function __construct(ProduitsRepository $produitsRepository)
    {
        $this->produitsRepository = $produitsRepository;
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show($id): Response
{
    $productDetails = $this->produitsRepository->findProductDetails($id);

    if (!$productDetails) {
        throw $this->createNotFoundException('No product found for id '.$id);
    }

    return $this->render('product/show.html.twig', [
        'product' => $productDetails

    ]);
}
}