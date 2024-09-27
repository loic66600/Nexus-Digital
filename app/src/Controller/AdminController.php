<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/avis', name: 'admin_avis')]
    public function avis(Request $request): Response
    {
        $sort = $request->query->get('sort', 'date_desc');
    
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(Avis::class, 'a')
            ->leftJoin('a.product', 'p');
    
        switch ($sort) {
            case 'date_asc':
                $queryBuilder->orderBy('a.dateNotice', 'ASC');
                break;
            case 'product_asc':
                $queryBuilder->orderBy('p.name', 'ASC');
                break;
            case 'product_desc':
                $queryBuilder->orderBy('p.name', 'DESC');
                break;
            case 'date_desc':
            default:
                $queryBuilder->orderBy('a.dateNotice', 'DESC');
                break;
        }
    
        $avis = $queryBuilder->getQuery()->getResult();
    
        return $this->render('admin/avis.html.twig', [
            'avis' => $avis,
        ]);
    }
    #[Route('/avis/{id}/valider', name: 'admin_avis_valider')]
    public function validerAvis(Avis $avis): Response
    {
        $avis->setValide(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'avis a été validé.');

        return $this->redirectToRoute('admin_avis');
    }

    #[Route('/avis/{id}/supprimer', name: 'admin_avis_supprimer')]
    public function supprimerAvis(Avis $avis): Response
    {
        $this->entityManager->remove($avis);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'avis a été supprimé.');

        return $this->redirectToRoute('admin_avis');
    }

    #[Route('/users', name: 'admin_users')]
    public function users(Request $request): Response
    {
        $sort = $request->query->get('sort', 'name_asc');
    
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->leftJoin('u.userAdresse', 'a');
    
        switch ($sort) {
            case 'name_desc':
                $queryBuilder->orderBy('u.lastName', 'DESC');
                break;
            case 'city_asc':
                $queryBuilder->orderBy('a.city', 'ASC');
                break;
            case 'city_desc':
                $queryBuilder->orderBy('a.city', 'DESC');
                break;
            case 'name_asc':
            default:
                $queryBuilder->orderBy('u.lastName', 'ASC');
                break;
        }
    
        $users = $queryBuilder->getQuery()->getResult();
    
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(Request $request, User $user): Response
    {
        // Ici, vous devriez ajouter la logique pour éditer l'utilisateur
        // Par exemple, créer un formulaire, le traiter, etc.

        return $this->render('admin/edit_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'admin_user_delete')]
    public function deleteUser(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé.');

        return $this->redirectToRoute('admin_users');
    }
}