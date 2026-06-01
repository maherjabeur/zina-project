<?php
// src/Service/NotificationService.php
namespace App\Service;

use App\Entity\Contact;
use App\Entity\Order;

class NotificationService
{
    public function __construct(private readonly PhpMailerService $mailer)
    {
    }

    public function sendOrderNotification(Order $order): void
    {
        $this->sendAdminNotification($order);

        if ($order->getCustomerEmail()) {
            $this->mailer->sendTemplate(
                $order->getCustomerEmail(),
                'Confirmation de votre commande - ' . $order->getOrderNumber(),
                'emails/order_confirmation.html.twig',
                ['order' => $order]
            );
        }
    }

    public function sendAdminNotification(Order $order): void
    {
        $this->mailer->sendTemplate(
            $this->mailer->getAdminEmail(),
            'Nouvelle commande - ' . $order->getOrderNumber(),
            'emails/new_order_admin.html.twig',
            ['order' => $order],
            $order->getCustomerEmail()
        );
    }

    public function sendStatusUpdateNotification(Order $order): void
    {
        if (!$order->getCustomerEmail()) {
            return;
        }

        $this->mailer->sendTemplate(
            $order->getCustomerEmail(),
            'Mise a jour de votre commande - ' . $order->getOrderNumber(),
            'emails/status_update.html.twig',
            ['order' => $order]
        );
    }

    public function sendContactNotification(Contact $contact): void
    {
        $this->mailer->sendTemplate(
            $this->mailer->getAdminEmail(),
            'Nouveau message de contact - ' . $contact->getName(),
            'emails/contact_notification.html.twig',
            ['contact' => $contact],
            $contact->getEmail()
        );
    }
}
