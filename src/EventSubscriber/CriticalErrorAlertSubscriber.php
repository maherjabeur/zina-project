<?php

namespace App\EventSubscriber;

use App\Service\PhpMailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CriticalErrorAlertSubscriber implements EventSubscriberInterface
{
    private const THROTTLE_SECONDS = 300;
    private const MAX_TRACE_LINES = 10;

    public function __construct(
        private readonly PhpMailerService $mailer,
        private readonly LoggerInterface $logger,
        private readonly string $alertRecipient,
        private readonly string $projectDir,
        private readonly string $environment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -64],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $throwable = $event->getThrowable();
        if (!$this->isCritical($throwable) || trim($this->alertRecipient) === '') {
            return;
        }

        $request = $event->getRequest();
        $fingerprint = $this->buildFingerprint($throwable, $request);

        if ($this->isThrottled($fingerprint)) {
            return;
        }

        try {
            $this->mailer->sendTemplate(
                $this->alertRecipient,
                sprintf('Alerte critique Bella Couture - HTTP %d', $this->getStatusCode($throwable)),
                'emails/critical_error_alert.html.twig',
                $this->buildEmailContext($throwable, $request, $fingerprint)
            );
        } catch (\Throwable $mailException) {
            $this->logger->error('Impossible d envoyer l alerte email d erreur critique.', [
                'alert_recipient' => $this->alertRecipient,
                'original_exception' => get_debug_type($throwable),
                'mail_exception' => get_debug_type($mailException),
                'mail_message' => $mailException->getMessage(),
            ]);
        }
    }

    private function isCritical(\Throwable $throwable): bool
    {
        if ($throwable instanceof HttpExceptionInterface) {
            return $throwable->getStatusCode() >= 500;
        }

        return true;
    }

    private function getStatusCode(\Throwable $throwable): int
    {
        if ($throwable instanceof HttpExceptionInterface) {
            return $throwable->getStatusCode();
        }

        return 500;
    }

    private function buildFingerprint(\Throwable $throwable, Request $request): string
    {
        return hash('sha256', implode('|', [
            get_debug_type($throwable),
            $throwable->getFile(),
            (string) $throwable->getLine(),
            (string) $request->attributes->get('_route', ''),
        ]));
    }

    private function isThrottled(string $fingerprint): bool
    {
        $directory = $this->projectDir . '/var/cache/error-alerts';
        $file = $directory . '/' . $fingerprint . '.stamp';

        if (is_file($file) && (time() - (int) filemtime($file)) < self::THROTTLE_SECONDS) {
            return true;
        }

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        @touch($file);

        return false;
    }

    private function buildEmailContext(\Throwable $throwable, Request $request, string $fingerprint): array
    {
        return [
            'environment' => $this->environment,
            'generated_at' => new \DateTimeImmutable(),
            'status_code' => $this->getStatusCode($throwable),
            'exception_class' => get_debug_type($throwable),
            'exception_message' => $throwable->getMessage(),
            'exception_file' => $throwable->getFile(),
            'exception_line' => $throwable->getLine(),
            'request_method' => $request->getMethod(),
            'request_uri' => $request->getUri(),
            'route' => (string) ($request->attributes->get('_route') ?: 'n/a'),
            'route_params' => $this->formatData($request->attributes->get('_route_params', [])),
            'query_params' => $this->formatData($request->query->all()),
            'client_ip' => $request->getClientIp() ?: 'n/a',
            'user_agent' => substr((string) $request->headers->get('User-Agent', 'n/a'), 0, 500),
            'referer' => (string) $request->headers->get('Referer', 'n/a'),
            'fingerprint' => substr($fingerprint, 0, 16),
            'trace_lines' => array_slice(
                array_filter(explode("\n", $throwable->getTraceAsString())),
                0,
                self::MAX_TRACE_LINES
            ),
        ];
    }

    private function formatData(mixed $data): string
    {
        $sanitized = $this->sanitizeValue($data);
        $json = json_encode($sanitized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $json ?: '{}';
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $clean = [];

            foreach ($value as $key => $item) {
                if ($this->isSensitiveKey((string) $key)) {
                    $clean[$key] = '[masque]';
                    continue;
                }

                $clean[$key] = $this->sanitizeValue($item);
            }

            return $clean;
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return sprintf('[%s]', get_debug_type($value));
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);

        return str_contains($key, 'password')
            || str_contains($key, 'token')
            || str_contains($key, 'secret')
            || str_contains($key, 'csrf');
    }
}
