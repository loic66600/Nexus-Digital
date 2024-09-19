<?php

// src/Controller/ReviewController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produits;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;

class ReviewController extends AbstractController
{
    #[Route('/product/{id}/review', name: 'submit_review', methods: ['POST'])]
    public function submitReview($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le produit par son ID
        $product = $entityManager->getRepository(Produits::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        // Créer un nouvel avis
        $avis = new Avis();
        $avis->setProduct($product);
        $avis->setNote((int)$request->request->get('_rating'));
        $avis->setComment($request->request->get('_comment'));
        $avis->setDateNotice(new \DateTime());
        // Vous pouvez ajouter plus de logique pour récupérer l'utilisateur connecté par exemple

        // Sauvegarder l'avis
        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->redirectToRoute('product_show', ['id' => $id]);
    }
}
