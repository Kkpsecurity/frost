<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences for email
        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_payment.payment_success')
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->payment->amount, 2);

        return (new MailMessage)
            ->subject('Payment Successful - ' . $orderNumber)
            ->greeting('Payment Received!')
            ->line('Your payment of **' . $amount . '** has been successfully processed.')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Payment Method:** ' . ucfirst($this->payment->payment_method))
            ->line('**Transaction ID:** ' . $this->payment->transaction_id)
            ->action('View Order Details', route('student.orders.show', $this->order->id))
            ->line('Thank you for your purchase! You can now access your course materials.')
            ->line('If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Payment Successful',
            'message' => 'Your payment for ' . ($this->order->Course->title ?? 'course') . ' has been processed.',
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'transaction_id' => $this->payment->transaction_id,
            'icon' => 'circle-check',
            'color' => 'success',
            'priority' => 'high',
            'url' => route('student.orders.show', $this->order->id),
        ];
    }
}
