<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class DefaultPaymentUpdatedNotification extends Notification implements ShouldQueue
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
            'payment.default_payment_updated',
            config('user_notifications.notifications.payment.default_payment_updated.channels', ['database']),
            (bool) config('user_notifications.notifications.payment.default_payment_updated.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $methodDetails = $this->getMethodDetails();

        return (new MailMessage)
            ->subject('Default Payment Method Updated')
            ->greeting('Default Payment Method Changed')
            ->line('Your default payment method has been updated.')
            ->line('**New Default:** ' . $methodDetails)
            ->line('This payment method will be used for future purchases unless you specify otherwise.')
            ->action('View Payment Methods', route('account.index', ['section' => 'payments']))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Default Payment Updated',
            'message' => 'Default payment method changed to ' . $this->getMethodDetails(),
            'icon' => 'star',
            'color' => 'info',
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
