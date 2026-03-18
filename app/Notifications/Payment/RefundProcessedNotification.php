<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class RefundProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected $order;
    protected $refundAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $refundAmount)
    {
        $this->order = $order;
        $this->refundAmount = $refundAmount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'payment.refund_processed',
            config('user_notifications.notifications.payment.refund_processed.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.payment.refund_processed.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->refundAmount, 2);

        return (new MailMessage)
            ->subject('Refund Completed - ' . $orderNumber)
            ->greeting('Refund Processed')
            ->line('Your refund of **' . $amount . '** has been successfully processed.')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Refund Date:** ' . now()->format('M j, Y'))
            ->line('The funds should appear in your account within 5-10 business days, depending on your financial institution.')
            ->action('View Order Details', route('order.completed', $this->order->id))
            ->line('Thank you for your patience. If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Refund Completed',
            'message' => 'Refund of $' . number_format($this->refundAmount, 2) . ' processed for ' . $orderNumber,
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'refund_amount' => $this->refundAmount,
            'refund_date' => now()->toDateString(),
            'icon' => 'circle-check',
            'color' => 'success',
            'priority' => 'high',
            'url' => route('order.completed', $this->order->id),
        ];
    }
}
