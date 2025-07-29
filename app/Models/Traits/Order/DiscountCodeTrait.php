<?php

declare(strict_types=1);

namespace App\Models\Traits\Order;

use App\Services\RCache;
use App\Models\DiscountCode;


trait DiscountCodeTrait
{


    public function ApplyDiscountCode(string $code): ?string
    {

        if (! $DiscountCode = $this->FindDiscountCode($code)) {
            return 'Unknown Discount Code';
        }

        if ($error = $this->ValidateDiscountCode($DiscountCode)) {
            return $error;
        }


        //
        // apply Discount Code
        //

        $this->update(['discount_code_id' => $DiscountCode->id]);
        $this->_CalcPrice();


        //
        // apply discounted price to all payments
        //

        foreach ($this->AllPayments() as $Payment) {
            $Payment->update(['total_price' => $this->total_price]);
        }


        //
        // no errors
        //

        return null;
    }


    public function FindDiscountCode(string $code): ?DiscountCode
    {

        foreach (RCache::DiscountCodes() as $DiscountCode) {
            if (strtolower($code) === strtolower($DiscountCode->code)) {
                return $DiscountCode;
            }
        }

        return null;
    }


    public function ValidateDiscountCode(DiscountCode $DiscountCode): ?string
    {

        if ($DiscountCode->course_id && $DiscountCode->course_id != $this->course_id) {
            return 'Discount Code Does Not Apply';
        }

        if ($DiscountCode->IsExpired()) {
            return 'Discount Code Expired';
        }

        if ($DiscountCode->max_count && $DiscountCode->max_count <= $DiscountCode->TimesUsed()) {
            return 'Discount Code Usage Limit Exceeded';
        }

        return null;
    }
}
