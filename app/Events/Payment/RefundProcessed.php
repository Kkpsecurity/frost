<?php

namespace App\Events\Payment;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $refundAmount;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, float $refundAmount)
    {
        $this->order = $order;
        $this->refundAmount = $refundAmount;
    }
}
