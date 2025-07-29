<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file DiscountCodesUsed.php
 * @brief Command to display used discount codes.
 * @details Lists all orders that used a specific discount code, showing the date, student name, and email.
 */

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

use App\Services\RCache;

use App\Models\Order;
use App\Models\DiscountCode;


class DiscountCodesUsed extends Command
{

    protected $signature   = 'command:discount_codes_used {id}';

    protected $description = 'Discount Codes Used';


    public function handle(): int
    {


        $DiscountCode = RCache::DiscountCodes($this->argument('id'));


        $Orders = Order::where('discount_code_id', $DiscountCode->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'DESC')
            ->with('user')
            ->get();



        $records = [];

        foreach ($Orders as $Order) {
            $records[] = [

                Carbon::parse($Order->completed_at)->tz('America/New_York')->isoformat('ddd YYYY-MM-DD HH:mm'),
                $Order->User->fullname(),
                $Order->User->email,

            ];
        }


        $this->line('Discount Codes used: ' . count($records) . "/{$DiscountCode->max_count}");

        $this->table(
            ['Date', 'Student', 'Email'],
            $records
        );


        return 0;
    }
}
