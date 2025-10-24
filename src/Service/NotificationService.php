<?php
// src/Service/NotificationService.php
namespace App\Service;

use App\Entity\Order;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class NotificationService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendOrderNotification(Order $order): void
    {
        // Email à l'admin
        $this->sendAdminNotification($order);

        // Email de confirmation au client seulement si email valide
        if ($order->getCustomerEmail()) {
            $customerEmail = (new TemplatedEmail())
                ->from('noreply@boutique-femme.com')
                ->to($order->getCustomerEmail())
                ->subject('Confirmation de votre commande - ' . $order->getOrderNumber())
                ->htmlTemplate('emails/order_confirmation.html.twig')
                ->context([
                    'order' => $order,
                ]);

            $this->mailer->send($customerEmail);
        }
    }

    public function sendAdminNotification(Order $order): void
    {
        // Email à l'admin seulement
        $adminEmail = (new TemplatedEmail())
            ->from('noreply@boutique-femme.com')
            ->to('admin@boutique-femme.com')
            ->subject('Nouvelle commande - ' . $order->getOrderNumber())
            ->htmlTemplate('emails/new_order_admin.html.twig')
            ->context([
                'order' => $order,
            ]);

        $this->mailer->send($adminEmail);
    }

    public function sendStatusUpdateNotification(Order $order): void
    {
        // Envoyer la mise à jour seulement si l'email est disponible
        if ($order->getCustomerEmail()) {
            $email = (new TemplatedEmail())
                ->from('noreply@boutique-femme.com')
                ->to($order->getCustomerEmail())
                ->subject('Mise à jour de votre commande - ' . $order->getOrderNumber())
                ->htmlTemplate('emails/status_update.html.twig')
                ->context([
                    'order' => $order,
                ]);

            $this->mailer->send($email);
        }
    }
    public function sendContactNotification(Contact $contact): void
{
    // Email à l'admin
    $email = (new TemplatedEmail())
        ->from('noreply@boutique-femme.com')
        ->to('contact@boutique-femme.com')
        ->subject('Nouveau message de contact - ' . $contact->getName())
        ->htmlTemplate('emails/contact_notification.html.twig')
        ->context([
            'contact' => $contact,
        ]);

    $this->mailer->send($email);
}
}