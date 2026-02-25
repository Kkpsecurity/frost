<?php

namespace App\Events\Verification;

use App\Models\Validation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a validation (ID card OR headshot) is rejected by an instructor/admin.
 */
class ValidationRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Validation $validation     The validation record that was rejected
     * @param string     $validationType 'id_card' | 'headshot'
     * @param string     $reason         Human-readable rejection reason
     */
    public function __construct(
        public Validation $validation,
        public string $validationType,
        public string $reason,
    ) {}
}
