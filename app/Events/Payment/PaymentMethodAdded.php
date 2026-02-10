<?php

namespace App\Events\Payment;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentMethodAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $paymentMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, array $paymentMethod)
    {
        $this->user = $user;
        $this->paymentMethod = $paymentMethod;
    }
}
