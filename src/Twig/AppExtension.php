<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Settings;
use App\Repository\OrderRepository;
use App\Repository\SettingRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private OrderRepository $orderRepository;
    private SettingRepository $settingRepository;

    public function __construct(OrderRepository $orderRepository, SettingRepository $settingRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->settingRepository = $settingRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_pending_orders', [$this, 'countPendingOrders']),
            new TwigFunction('site_settings', [$this, 'getSiteSettings']),
        ];
    }

    public function countPendingOrders(): int
    {
        return $this->orderRepository->countPendingOrders();
    }

    public function getSiteSettings(): ?Settings
    {
        return $this->settingRepository->getCurrentSettings();
    }
}
