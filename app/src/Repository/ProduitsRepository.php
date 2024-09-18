<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    public function AllProduit()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function findByCategoryName(string $categoryName)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->where('c.name = :name')
            ->setParameter('name', $categoryName)
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(?string $categoryName = null)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c');
    
        if ($categoryName) {
            $queryBuilder->andWhere('c.name = :categoryName')
                ->setParameter('categoryName', urldecode($categoryName));
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
}