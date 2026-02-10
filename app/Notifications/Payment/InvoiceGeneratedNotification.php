<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $invoiceNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $invoiceNumber = null)
    {
        $this->order = $order;
        $this->invoiceNumber = $invoiceNumber ?? 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences for email
        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_payment.invoice_generated')
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->order->total_price, 2);

        return (new MailMessage)
            ->subject('Invoice Available - ' . $this->invoiceNumber)
            ->greeting('Your Invoice is Ready')
            ->line('Your invoice for order ' . $orderNumber . ' is now available.')
            ->line('**Invoice Number:** ' . $this->invoiceNumber)
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Amount:** ' . $amount)
            ->line('**Date:** ' . ($this->order->completed_at ?? $this->order->created_at)->format('M j, Y'))
            ->action('Download Invoice', route('student.invoice', $this->order->id))
            ->line('You can download and print your invoice at any time from your account.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Invoice Available',
            'message' => 'Invoice ' . $this->invoiceNumber . ' for ' . ($this->order->Course->title ?? 'order') . ' is ready to download.',
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'invoice_number' => $this->invoiceNumber,
            'amount' => $this->order->total_price,
            'icon' => 'file-invoice-dollar',
            'color' => 'info',
            'priority' => 'low',
            'url' => route('student.invoice', $this->order->id),
        ];
    }
}
