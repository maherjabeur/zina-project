<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;

class PhpMailerService
{
    public function __construct(
        private readonly Environment $twig,
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly string $encryption,
        private readonly bool $smtpAuth,
        private readonly string $fromEmail,
        private readonly string $fromName,
        private readonly string $adminEmail,
        private readonly bool $dryRun,
        private readonly string $projectDir,
    ) {
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function sendTemplate(
        string $to,
        string $subject,
        string $template,
        array $context = [],
        ?string $replyTo = null
    ): ?string {
        $html = $this->twig->render($template, $context);

        return $this->sendHtml($to, $subject, $html, $replyTo);
    }

    public function sendHtml(string $to, string $subject, string $html, ?string $replyTo = null): ?string
    {
        if ($this->dryRun) {
            return $this->writeDryRunEmail($to, $subject, $html);
        }

        if (trim($this->host) === '') {
            throw new \RuntimeException('Configuration PHPMailer incomplete: PHPMAILER_HOST est vide.');
        }

        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPAuth = $this->smtpAuth;

        if ($this->smtpAuth) {
            $mail->Username = $this->username;
            $mail->Password = $this->password;
        }

        $encryption = strtolower(trim($this->encryption));
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls' || $encryption === 'starttls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addAddress($to);

        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = $this->htmlToText($html);
        $mail->send();

        return null;
    }

    private function writeDryRunEmail(string $to, string $subject, string $html): string
    {
        $directory = $this->projectDir . '/var/mail';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $safeSubject = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($subject)) ?: 'email';
        $filename = sprintf('%s/%s-%s.html', $directory, date('Ymd-His'), trim($safeSubject, '-'));

        $content = sprintf(
            "<!-- To: %s -->\n<!-- Subject: %s -->\n%s",
            htmlspecialchars($to, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'),
            $html
        );

        file_put_contents($filename, $content);

        return $filename;
    }

    private function htmlToText(string $html): string
    {
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n\n", $html);

        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
