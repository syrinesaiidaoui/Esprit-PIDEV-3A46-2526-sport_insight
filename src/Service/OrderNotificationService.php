<?php

namespace App\Service;

use App\Entity\ProductOrder\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class OrderNotificationService
{
    private const FROM_EMAIL = 'no-reply@sport-insight.local';

    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @param Order[] $orders
     */
    public function sendOrderConfirmation(string $recipientEmail, string $customerName, array $orders): void
    {
        if ($recipientEmail === '' || empty($orders)) {
            return;
        }

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(self::FROM_EMAIL)
                ->to($recipientEmail)
                ->subject('Order Confirmation - Sport Insight')
                ->htmlTemplate('emails/order_confirmation.html.twig')
                ->context([
                    'customerName' => $customerName,
                    'orders' => $orders,
                ])
        );
    }

    /**
     * @param Order[] $orders
     */
    public function sendPaymentConfirmation(string $recipientEmail, string $customerName, array $orders): void
    {
        if ($recipientEmail === '' || empty($orders)) {
            return;
        }

        $totalPaid = 0.0;
        foreach ($orders as $order) {
            if ($order instanceof Order) {
                $totalPaid += $order->getComputedTotal();
            }
        }

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(self::FROM_EMAIL)
                ->to($recipientEmail)
                ->subject('Payment Confirmation - Sport Insight')
                ->htmlTemplate('emails/payment_confirmation.html.twig')
                ->context([
                    'customerName' => $customerName,
                    'orders' => $orders,
                    'totalPaid' => round($totalPaid, 2),
                ])
        );
    }

    public function sendShippingNotification(Order $order): void
    {
        $recipientEmail = (string) ($order->getContactEmail() ?? '');
        if ($recipientEmail === '') {
            return;
        }

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(self::FROM_EMAIL)
                ->to($recipientEmail)
                ->subject(sprintf('Shipping Update - Order #%d', (int) $order->getId()))
                ->htmlTemplate('emails/shipping_notification.html.twig')
                ->context([
                    'order' => $order,
                ])
        );
    }
}
