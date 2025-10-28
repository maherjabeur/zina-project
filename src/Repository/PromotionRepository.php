<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    // Fetch active promotions for a specific product
    public function findActivePromotionsForProduct(int $productId): array
    {
        
        return $this->createQueryBuilder('p')
            ->where('p.product = :productId')
            ->andWhere('p.isActive = :isActive')
            ->setParameter('productId', $productId)
            ->setParameter('isActive', true)
            ->orderBy('p.discount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Fetch all promotions for a specific product (including inactive)
    public function findPromotionsForProduct(int $productId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.product = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('p.isActive', 'DESC')
            ->addOrderBy('p.discount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Find currently valid promotions (active and within date range)
    public function findValidPromotions(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.isActive = :isActive')
            ->andWhere('(p.startDate IS NULL OR p.startDate <= :now)')
            ->andWhere('(p.endDate IS NULL OR p.endDate >= :now)')
            ->setParameter('isActive', true)
            ->setParameter('now', $now)
            ->orderBy('p.discount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Find promotions that are about to expire
    public function findExpiringPromotions(int $days = 7): array
    {
        $now = new \DateTime();
        $expiryDate = (new \DateTime())->modify("+{$days} days");

        return $this->createQueryBuilder('p')
            ->where('p.isActive = :isActive')
            ->andWhere('p.endDate IS NOT NULL')
            ->andWhere('p.endDate BETWEEN :now AND :expiryDate')
            ->setParameter('isActive', true)
            ->setParameter('now', $now)
            ->setParameter('expiryDate', $expiryDate)
            ->orderBy('p.endDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Find active promotions with product count
    public function findWithProductCount(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'COUNT(prod.id) as productCount')
            ->leftJoin('p.product', 'prod')
            ->groupBy('p.id')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Find promotions by status (active/inactive)
    public function findByStatus(bool $isActive): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :isActive')
            ->setParameter('isActive', $isActive)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Find the best promotion for a product
    public function findBestPromotionForProduct(int $productId): ?Promotion
    {
        $promotions = $this->findActivePromotionsForProduct($productId);

        if (empty($promotions)) {
            return null;
        }

        // Return the promotion with the highest discount
        return array_reduce($promotions, function ($best, $current) {
            if ($best === null) {
                return $current;
            }
            return $current->getDiscount() > $best->getDiscount() ? $current : $best;
        });
    }

    // Count promotions by status
    public function countByStatus(bool $isActive): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.isActive = :isActive')
            ->setParameter('isActive', $isActive)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Find promotions that need to be activated (start date reached)
    public function findPromotionsToActivate(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.isActive = :isActive')
            ->andWhere('p.startDate IS NOT NULL')
            ->andWhere('p.startDate <= :now')
            ->setParameter('isActive', false)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    // Find promotions that need to be deactivated (end date passed)
    public function findPromotionsToDeactivate(): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.isActive = :isActive')
            ->andWhere('p.endDate IS NOT NULL')
            ->andWhere('p.endDate < :now')
            ->setParameter('isActive', true)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}