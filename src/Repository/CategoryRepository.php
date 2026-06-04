<?php
// src/Repository/CategoryRepository.php
namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findActiveCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.position', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, int>
     */
    public function countActiveProductsBySlug(): array
    {
        $rows = $this->createQueryBuilder('c')
            ->select('c.slug AS slug')
            ->addSelect('COUNT(p.id) AS productCount')
            ->leftJoin('c.products', 'p', 'WITH', 'p.isActive = :productActive')
            ->where('c.isActive = :active')
            ->setParameter('active', true)
            ->setParameter('productActive', true)
            ->groupBy('c.id')
            ->addGroupBy('c.slug')
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) $row['slug']] = (int) $row['productCount'];
        }

        return $counts;
    }

    public function findForNavigation(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.position', 'ASC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }

public function findWithProductCount(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(p.id) as productCount')
            ->leftJoin('c.products', 'p', 'WITH', 'p.isActive = :productActive')
            ->setParameter('productActive', true)
            ->groupBy('c.id')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
