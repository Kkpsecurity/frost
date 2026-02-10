<?php

namespace App\Events\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $payment;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, Payment $payment, string $reason = 'Payment declined')
    {
        $this->order = $order;
        $this->payment = $payment;
        $this->reason = $reason;
    }
}
