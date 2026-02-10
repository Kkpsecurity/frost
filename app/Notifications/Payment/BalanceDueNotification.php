<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BalanceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $amountDue;
    protected $dueDate;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $amountDue, $dueDate = null)
    {
        $this->order = $order;
        $this->amountDue = $amountDue;
        $this->dueDate = $dueDate ?? now()->addDays(7);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Balance due always sent via database and email (critical financial notification)
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->amountDue, 2);
        $dueDate = is_string($this->dueDate) ? $this->dueDate : $this->dueDate->format('M j, Y');

        return (new MailMessage)
            ->error()
            ->subject('Payment Required - ' . $orderNumber)
            ->greeting('Balance Due')
            ->line('You have an outstanding balance that requires payment.')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Amount Due:** ' . $amount)
            ->line('**Due Date:** ' . $dueDate)
            ->line('Please complete your payment to maintain access to your course materials.')
            ->action('Make Payment', route('enrollment.enroll', $this->order->course_id))
            ->line('If you have already made this payment, please allow 24-48 hours for processing.')
            ->line('For questions about your balance, please contact our billing department.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $dueDate = is_string($this->dueDate) ? $this->dueDate : $this->dueDate->format('Y-m-d');

        return [
            'title' => 'Balance Due',
            'message' => 'Payment of $' . number_format($this->amountDue, 2) . ' due for ' . $orderNumber,
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'amount_due' => $this->amountDue,
            'due_date' => $dueDate,
            'icon' => 'triangle-exclamation',
            'color' => 'danger',
            'priority' => 'critical',
            'url' => route('enrollment.enroll', $this->order->course_id),
        ];
    }
}
