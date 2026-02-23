<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected $order;
    protected $payment;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $payment, $reason = 'Payment declined')
    {
        $this->order = $order;
        $this->payment = $payment;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'payment.payment_failed',
            config('user_notifications.notifications.payment.payment_failed.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.payment.payment_failed.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);
        $amount = '$' . number_format($this->payment->amount, 2);

        return (new MailMessage)
            ->error()
            ->subject('Payment Failed - ' . $orderNumber)
            ->greeting('Payment Issue')
            ->line('We were unable to process your payment of **' . $amount . '**.')
            ->line('**Order:** ' . $orderNumber)
            ->line('**Course:** ' . ($this->order->Course->title ?? 'N/A'))
            ->line('**Reason:** ' . $this->reason)
            ->line('**What to do next:**')
            ->line('• Verify your payment method details are correct')
            ->line('• Ensure sufficient funds are available')
            ->line('• Try a different payment method')
            ->line('• Contact your bank if the issue persists')
            ->action('Retry Payment', route('enrollment.enroll', $this->order->course_id))
            ->line('If you continue to experience issues, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = 'ORD-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT);

        return [
            'title' => 'Payment Failed',
            'message' => 'Unable to process payment for ' . ($this->order->Course->title ?? 'course') . '. ' . $this->reason,
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'reason' => $this->reason,
            'icon' => 'circle-xmark',
            'color' => 'danger',
            'priority' => 'critical',
            'url' => route('enrollment.enroll', $this->order->course_id),
        ];
    }
}
