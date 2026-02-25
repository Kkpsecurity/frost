<?php

namespace App\Events\Verification;

use App\Models\Validation;
use App\Models\StudentUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a single validation (ID card OR headshot) is approved.
 *
 * $fullyVerified = true means BOTH validations are now approved → identity is fully verified.
 */
class ValidationApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Validation   $validation     The validation record that was approved
     * @param string       $validationType 'id_card' | 'headshot'
     * @param bool         $fullyVerified  True when both validations are now approved
     * @param StudentUnit|null $studentUnit Present when fullyVerified is true
     */
    public function __construct(
        public Validation $validation,
        public string $validationType,
        public bool $fullyVerified,
        public ?StudentUnit $studentUnit = null,
    ) {}
}
