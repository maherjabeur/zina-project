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
    // Ajoutez cette méthode pour la recherche
    public function search(string $query, ?string $categorySlug = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('p.isActive = :active')
            ->andWhere('c.isActive = :categoryActive')
            ->setParameter('active', true)
            ->setParameter('categoryActive', true);

        // Recherche dans le nom et la description
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->like('LOWER(p.name)', 'LOWER(:query)'),
                $qb->expr()->like('LOWER(p.description)', 'LOWER(:query)'),
                $qb->expr()->like('LOWER(p.color)', 'LOWER(:query)')
            )
        )
        ->setParameter('query', '%' . $query . '%');

        // Filtre par catégorie si spécifié
        if ($categorySlug) {
            $qb->andWhere('c.slug = :categorySlug')
               ->setParameter('categorySlug', $categorySlug);
        }

        return $qb
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Méthode pour la recherche avec suggestions
    public function findSearchSuggestions(string $query, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.isActive = :active')
            ->andWhere('LOWER(p.name) LIKE LOWER(:query)')
            ->setParameter('active', true)
            ->setParameter('query', '%' . $query . '%')
            ->groupBy('p.name')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
