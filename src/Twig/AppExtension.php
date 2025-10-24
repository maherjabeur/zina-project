<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Repository\OrderRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_pending_orders', [$this, 'countPendingOrders']),
        ];
    }

    public function countPendingOrders(): int
    {
        return $this->orderRepository->countPendingOrders();
    }
}