<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentMethodRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $paymentMethod;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences for email
        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_payment.payment_method_removed')
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $methodType = ucfirst($this->paymentMethod['type']);
        $methodDetails = $this->getMethodDetails();

        return (new MailMessage)
            ->subject('Payment Method Removed')
            ->greeting('Payment Method Removed')
            ->line('A ' . $methodType . ' payment method has been removed from your account.')
            ->line('**Details:** ' . $methodDetails)
            ->line('**Removed:** ' . now()->format('M j, Y g:i A'))
            ->line('If you did not make this change, please contact support immediately and secure your account.')
            ->action('Manage Payment Methods', route('account.payments'))
            ->line('You can add a new payment method at any time from your account settings.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Method Removed',
            'message' => ucfirst($this->paymentMethod['type']) . ' payment method removed: ' . $this->getMethodDetails(),
            'payment_method' => $this->paymentMethod,
            'icon' => 'trash',
            'color' => 'warning',
            'priority' => 'medium',
            'url' => route('account.payments'),
        ];
    }

    /**
     * Get a readable description of the payment method
     */
    protected function getMethodDetails(): string
    {
        if ($this->paymentMethod['type'] === 'card') {
            $brand = ucfirst($this->paymentMethod['brand'] ?? 'Card');
            return $brand . ' ending in ' . ($this->paymentMethod['last4'] ?? '****');
        } elseif ($this->paymentMethod['type'] === 'paypal') {
            return 'PayPal (' . ($this->paymentMethod['email'] ?? 'Account') . ')';
        }

        return ucfirst($this->paymentMethod['type']);
    }
}
