<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file UpdateDiscountCodeCount.php
 * @brief Command for updating the count of a discount code.
 * @details This command allows the user to update the maximum count of a discount code.
 */

use Illuminate\Console\Command;

use App\Services\RCache;


class UpdateDiscountCodeCount extends Command
{

    protected $signature   = 'command:update_discount_code_count {id}';
    protected $description = 'Update Discount Code Count';


    public function handle(): int
    {

        $DiscountCode = RCache::DiscountCodes($this->argument('id'));

        if (! $DiscountCode->max_count) {
            $this->error("This DiscountCode does not have a max_count");
            return 1;
        }

        $this->line("Code:      {$DiscountCode->code}");
        $this->line("Max Count: {$DiscountCode->max_count}");
        $this->line("Client:    {$DiscountCode->client}");

        //
        //
        //

        $add_count = '';

        // 2024-10 allow negative value
        while (! preg_match('/^\-?\d+$/', $add_count)) {
            $add_count = $this->ask('Add how many?');
        }

        $new_count = $DiscountCode->max_count + (int) $add_count;

        $this->line("New Max Count: {$new_count}");

        if (! $this->confirm('Confirm?')) {
            return 1;
        }

        //
        //
        //

        $DiscountCode->max_count = $new_count;
        $DiscountCode->save();
        $this->line(print_r($DiscountCode->toArray(), true));

        return 0;
    }
}
