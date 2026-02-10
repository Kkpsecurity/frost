<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundInitiatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $refundAmount;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $refundAmount, $reason = null)
    {
        $this->order = $order;
        $this->refundAmount = $refundAmount;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Refunds always sent via database and email (critical financial transaction)
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->refundAmount, 2);

        $mail = (new MailMessage)
            ->subject('Refund Initiated - ' . $orderNumber)
            ->greeting('Refund In Progress')
            ->line('A refund of **' . $amount . '** has been initiated for your order.')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'));

        if ($this->reason) {
            $mail->line('**Reason:** ' . $this->reason);
        }

        $mail->line('The refund will be processed within 5-10 business days and will appear on your original payment method.')
            ->line('You will receive another notification once the refund has been completed.')
            ->action('View Order Details', route('student.orders.show', $this->order->id))
            ->line('If you have any questions about this refund, please contact our support team.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Refund Initiated',
            'message' => 'Refund of $' . number_format($this->refundAmount, 2) . ' initiated for ' . $orderNumber,
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'refund_amount' => $this->refundAmount,
            'reason' => $this->reason,
            'icon' => 'rotate-left',
            'color' => 'info',
            'priority' => 'high',
            'url' => route('student.orders.show', $this->order->id),
        ];
    }
}
