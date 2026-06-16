<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public const SUPPORTED_LOCALES = ['ar', 'fr'];
    public const DEFAULT_LOCALE = 'ar';

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->hasSession() ? $request->getSession() : null;
        $locale = $request->query->get('locale');

        if (!is_string($locale) || !in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $request->request->get('locale');
        }

        if (!is_string($locale) || !in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = $session?->get('_locale');
        }

        if (!is_string($locale) || !in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = self::DEFAULT_LOCALE;
        }

        $request->setLocale($locale);
        $request->attributes->set('_locale', $locale);
        $session?->set('_locale', $locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
