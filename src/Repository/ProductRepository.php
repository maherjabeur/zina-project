<?php
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }



    /**
     * Trouve les produits par catégorie
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les produits en vedette
     */
    public function findFeatured(int $maxResults = 8): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }
    public function findByCategorySlug(string $slug): array
{
    return $this->createQueryBuilder('p')
        ->join('p.category', 'c')
        ->where('c.slug = :slug')
        ->andWhere('p.isActive = :active')
        ->andWhere('c.isActive = :categoryActive')
        ->setParameter('slug', $slug)
        ->setParameter('active', true)
        ->setParameter('categoryActive', true)
        ->orderBy('p.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findByFilters(?string $categorySlug = null, ?string $size = null): array
{
    $qb = $this->createQueryBuilder('p')
        ->join('p.category', 'c')
        ->where('p.isActive = :active')
        ->andWhere('c.isActive = :categoryActive')
        ->setParameter('active', true)
        ->setParameter('categoryActive', true)
        ->orderBy('p.createdAt', 'DESC');

    if ($categorySlug) {
        $qb->andWhere('c.slug = :categorySlug')
           ->setParameter('categorySlug', $categorySlug);
    }

    if ($size) {
        $qb->andWhere('p.size = :size')
           ->setParameter('size', $size);
    }

    return $qb->getQuery()->getResult();
}
}