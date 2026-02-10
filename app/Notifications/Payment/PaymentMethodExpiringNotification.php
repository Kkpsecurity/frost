<?php

namespace App\Notifications\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentMethodExpiringNotification extends Notification implements ShouldQueue
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
            ->where('key', 'notification_payment.payment_method_expiring')
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $brand = ucfirst($this->paymentMethod['brand'] ?? 'Card');
        $last4 = $this->paymentMethod['last4'] ?? '****';
        $expMonth = str_pad($this->paymentMethod['exp_month'] ?? '00', 2, '0', STR_PAD_LEFT);
        $expYear = $this->paymentMethod['exp_year'] ?? '00';

        return (new MailMessage)
            ->subject('Payment Method Expiring Soon')
            ->greeting('Update Your Payment Method')
            ->line('Your saved payment method will expire soon.')
            ->line('**Card:** ' . $brand . ' ending in ' . $last4)
            ->line('**Expires:** ' . $expMonth . '/' . $expYear)
            ->line('To avoid any interruption in service, please update your payment information.')
            ->action('Update Payment Method', route('account.payments'))
            ->line('Thank you for keeping your account information current!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $brand = ucfirst($this->paymentMethod['brand'] ?? 'Card');
        $last4 = $this->paymentMethod['last4'] ?? '****';
        $expMonth = str_pad($this->paymentMethod['exp_month'] ?? '00', 2, '0', STR_PAD_LEFT);
        $expYear = $this->paymentMethod['exp_year'] ?? '00';

        return [
            'title' => 'Payment Method Expiring',
            'message' => $brand . ' ending in ' . $last4 . ' expires ' . $expMonth . '/' . $expYear,
            'payment_method' => $this->paymentMethod,
            'icon' => 'calendar-xmark',
            'color' => 'warning',
            'priority' => 'high',
            'url' => route('account.payments'),
        ];
    }
}
