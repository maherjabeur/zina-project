<?php
// src/Repository/SizeRepository.php
namespace App\Repository;

use App\Entity\Size;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Size::class);
    }

    public function findActiveSizes(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.type = :type')
            ->andWhere('s.isActive = :active')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithProductCount(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'COUNT(p.id) as productCount')
            ->leftJoin('s.products', 'p', 'WITH', 'p.isActive = :productActive')
            ->where('s.isActive = :active')
            ->setParameter('active', true)
            ->setParameter('productActive', true)
            ->groupBy('s.id')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAvailableSizesForCategory(string $categoryType): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.type = :type')
            ->andWhere('s.isActive = :active')
            ->setParameter('type', $categoryType)
            ->setParameter('active', true)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}