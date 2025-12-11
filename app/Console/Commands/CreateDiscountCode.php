<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file CreateDiscountCode.php
 * @brief Command to create a discount code.
 * @details This command allows the creation of a discount code with various parameters such as course, price, and client.
 */

use Illuminate\Console\Command;

use App\Services\RCache;

use App\Helpers\PgTk;
use App\Models\DiscountCode;


class CreateDiscountCode extends Command
{

    protected $signature   = 'command:create_discount_code';
    protected $description = 'Create Discount Code';


    public function handle(): int
    {

        $code = '';
        while (! $code) {
            $code = $this->ask('Code (required)');
        }

        if ($DiscountCode = DiscountCode::firstWhere('code', $code)) {

            if (! $this->confirm('Delete existing DiscountCode?')) {
                return 1;
            }

            $DiscountCode->delete();
            $this->info('Deleted existing DiscountCode');
        }

        //
        //
        //

        $course_title = $this->choice(
            'Course',
            RCache::Courses()->where('is_active', true)->pluck('title', 'id')->toArray()
        );
        // this is dumb
        $course_id = RCache::Courses()->firstWhere('title', $course_title)->id;

        //
        //
        //

        $set_price = $this->ask('Price (opt)');

        $percent   = is_null($set_price)
            ? $this->ask('Percent (opt)')
            : null;

        $max_count = $this->ask('Max Count (opt)');

        $client    = $this->ask('Client (opt)');

        //
        //
        //

        $DiscountCode = DiscountCode::create([
            'code'      => $code,
            'course_id' => $course_id,
            'set_price' => $set_price,
            'percent'   => $percent,
            'max_count' => $max_count,
            'client'    => $client,
            'uuid'      => PgTk::UUID_v4(),
        ]);


        $this->info('Created DiscountCode');
        $this->line(print_r($DiscountCode->toArray(), true));
        $this->line(route('discount_codes.usage', $DiscountCode));
        $this->line('');

        return 0;
    }
}
