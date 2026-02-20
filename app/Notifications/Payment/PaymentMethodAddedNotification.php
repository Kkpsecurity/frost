<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class PaymentMethodAddedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected array $paymentMethod;

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
        return $this->preferredChannels(
            $notifiable,
            'payment.payment_method_added',
            config('user_notifications.notifications.payment.payment_method_added.channels', ['database']),
            (bool) config('user_notifications.notifications.payment.payment_method_added.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $methodType = ucfirst($this->paymentMethod['type']);
        $methodDetails = $this->getMethodDetails();

        return (new MailMessage)
            ->subject('Payment Method Added')
            ->greeting('New Payment Method Added')
            ->line('A new ' . $methodType . ' payment method has been added to your account.')
            ->line('**Details:** ' . $methodDetails)
            ->line('**Added:** ' . now()->format('M j, Y g:i A'))
            ->line('If you did not make this change, please contact support immediately.')
            ->action('Manage Payment Methods', route('account.index', ['section' => 'payments']))
            ->line('Thank you for keeping your payment information up to date!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Method Added',
            'message' => 'New ' . ucfirst($this->paymentMethod['type']) . ' payment method added: ' . $this->getMethodDetails(),
            'icon' => 'credit-card',
            'color' => 'success',
            'priority' => 'low',
            'url' => route('account.index', ['section' => 'payments']),
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
