<?php
// src/Repository/OrderRepository.php
namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Trouve les commandes récentes
     */
    public function findRecentOrders(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les commandes par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les commandes en attente
     */
    public function countPendingOrders(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.status = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFinancialSummary(array $statuses, ?\DateTimeInterface $start = null, ?\DateTimeInterface $end = null): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('COUNT(o.id) AS ordersCount')
            ->addSelect('COALESCE(SUM(o.total), 0) AS revenue')
            ->addSelect('COALESCE(SUM(o.discount), 0) AS discount')
            ->addSelect('COALESCE(SUM(o.shippingFee), 0) AS shipping')
            ->addSelect('COALESCE(AVG(o.total), 0) AS averageOrder')
            ->where('o.status IN (:statuses)')
            ->setParameter('statuses', $statuses);

        if ($start) {
            $queryBuilder
                ->andWhere('o.createdAt >= :start')
                ->setParameter('start', $start);
        }

        if ($end) {
            $queryBuilder
                ->andWhere('o.createdAt < :end')
                ->setParameter('end', $end);
        }

        $result = $queryBuilder->getQuery()->getSingleResult();

        return [
            'ordersCount' => (int) $result['ordersCount'],
            'revenue' => (float) $result['revenue'],
            'discount' => (float) $result['discount'],
            'shipping' => (float) $result['shipping'],
            'averageOrder' => (float) $result['averageOrder'],
            'itemsRevenue' => max(0, (float) $result['revenue'] - (float) $result['shipping']),
        ];
    }

    public function countByStatus(): array
    {
        $rows = $this->createQueryBuilder('o')
            ->select('o.status AS status')
            ->addSelect('COUNT(o.id) AS ordersCount')
            ->groupBy('o.status')
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['ordersCount'];
        }

        return $counts;
    }

    public function findTopProductsByRevenue(array $statuses, int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->select('COALESCE(p.name, :deletedProduct) AS productName')
            ->addSelect('SUM(i.quantity) AS soldQuantity')
            ->addSelect('SUM(i.total) AS revenue')
            ->join('o.items', 'i')
            ->leftJoin('i.product', 'p')
            ->where('o.status IN (:statuses)')
            ->groupBy('p.id')
            ->addGroupBy('p.name')
            ->orderBy('revenue', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('statuses', $statuses)
            ->setParameter('deletedProduct', 'Produit supprime')
            ->getQuery()
            ->getArrayResult();
    }
}
