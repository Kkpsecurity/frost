<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReceiptEmailedNotification extends Notification implements ShouldQueue
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
        // Receipts primarily database notification, email is the actual receipt
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->payment->amount, 2);

        return (new MailMessage)
            ->subject('Payment Receipt - ' . $orderNumber)
            ->greeting('Payment Receipt')
            ->line('Thank you for your payment!')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Amount Paid:** ' . $amount)
            ->line('**Payment Method:** ' . ucfirst($this->payment->payment_method))
            ->line('**Transaction ID:** ' . $this->payment->transaction_id)
            ->line('**Date:** ' . ($this->payment->processed_at ?? now())->format('M j, Y g:i A'))
            ->action('Download Receipt', route('student.invoice', $this->order->id))
            ->line('This receipt is for your records. You can download a PDF copy anytime from your account.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Receipt Emailed',
            'message' => 'Payment receipt for ' . $orderNumber . ' sent to your email.',
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'icon' => 'receipt',
            'color' => 'success',
            'priority' => 'low',
            'url' => route('student.invoice', $this->order->id),
        ];
    }
}
