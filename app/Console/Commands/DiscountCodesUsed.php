<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use App\RCache;
use App\Models\Order;
use App\Models\DiscountCode;


class DiscountCodesUsed extends Command
{

    protected $signature   = 'command:discount_codes_used {id}';

    protected $description = 'Discount Codes Used';


    public function handle() : int
    {


        $DiscountCode = RCache::DiscountCodes( $this->argument( 'id' ) );


        $Orders = Order::where( 'discount_code_id', $DiscountCode->id )
                       ->whereNotNull( 'completed_at' )
                       ->orderBy( 'completed_at', 'DESC' )
                       ->with( 'user' )
                       ->get();



        $records = [];

        foreach ( $Orders as $Order )
        {
            $records[] = [

                Carbon::parse( $Order->completed_at )->tz( 'America/New_York' )->isoformat( 'ddd YYYY-MM-DD HH:mm' ),
                $Order->User->fullname(),
                $Order->User->email,

            ];
        }


        $this->line( 'Discount Codes used: ' . count( $records ) . "/{$DiscountCode->max_count}" );

        $this->table(
            [ 'Date', 'Student', 'Email' ],
            $records
        );


        return 0;

    }

}
