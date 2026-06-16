<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Settings;
use App\Repository\OrderRepository;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private OrderRepository $orderRepository;
    private SettingRepository $settingRepository;
    private RequestStack $requestStack;

    public function __construct(OrderRepository $orderRepository, SettingRepository $settingRepository, RequestStack $requestStack)
    {
        $this->orderRepository = $orderRepository;
        $this->settingRepository = $settingRepository;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('count_pending_orders', [$this, 'countPendingOrders']),
            new TwigFunction('site_settings', [$this, 'getSiteSettings']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('localized', [$this, 'getLocalizedField']),
            new TwigFilter('localized_color', [$this, 'getLocalizedColor']),
            new TwigFilter('localized_size', [$this, 'getLocalizedSize']),
            new TwigFilter('localized_colors', [$this, 'getLocalizedColors']),
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

    public function getLocalizedField(mixed $subject, string $field): mixed
    {
        if (!is_object($subject)) {
            return null;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        $localizedGetter = 'getLocalized' . ucfirst($field);
        if (method_exists($subject, $localizedGetter)) {
            return $subject->{$localizedGetter}($locale);
        }

        if ($locale === 'ar') {
            $arabicGetter = 'get' . ucfirst($field) . 'Ar';
            if (method_exists($subject, $arabicGetter)) {
                $value = $subject->{$arabicGetter}();
                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        $getter = 'get' . ucfirst($field);
        return method_exists($subject, $getter) ? $subject->{$getter}() : null;
    }

    public function getLocalizedColors(mixed $subject): array
    {
        if (!is_object($subject) || !method_exists($subject, 'getLocalizedColors')) {
            return [];
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        return $subject->getLocalizedColors($locale);
    }

    public function getLocalizedColor(mixed $subject, ?string $color): ?string
    {
        if (!is_object($subject) || $color === null || !method_exists($subject, 'getLocalizedColor')) {
            return $color;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        return $subject->getLocalizedColor($color, $locale);
    }

    public function getLocalizedSize(mixed $subject, ?string $size): ?string
    {
        if (!is_object($subject) || $size === null || !method_exists($subject, 'getLocalizedSize')) {
            return $size;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        return $subject->getLocalizedSize($size, $locale);
    }
}
