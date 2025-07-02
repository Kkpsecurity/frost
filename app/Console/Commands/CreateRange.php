<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Range;
use App\Models\RangeDate;


class CreateRange extends Command
{

    protected $signature   = 'command:create_range';
    protected $description = 'Create Range';


    public function handle() : int
    {

        $this->error( 'Stopping' ); exit(1);


        $range_data = [

            'name'       => 'Southwest Florida Range & Gun Club',
            'city'       => 'Immokalee',
            'address'    => "31101 Nafi Dr\nImmokalee, FL 34142",
            'inst_name'  => 'Tony Lauer',
            'inst_email' => 'info@ppft.us',
            'inst_phone' => '(239) 300-1920',
            'price'      => 175.00,
            'times'      => 'By Appointment Only',
            'appt_only'  => true,
            'range_html' => <<<HTML
Students should have their own gun/gear/ammo.

This is an outdoor range; dress appropriately and bring water/drinks, snacks, hat, sunscreen, etc. If you are not sure what appropriate dress is, contact the instructor.

Other ranges, including indoor ranges, <i>may</i> be available; check with the instructor.

Contact the instructor for additional information.
HTML

        ];


        //
        //
        //


        if ( $Range = Range::firstWhere( 'city', $range_data[ 'city' ] ) )
        {

            if ( ! $this->confirm( 'Delete existing Range and RangeDates?' ) )
            {
                return 1;
            }

            $deleted = RangeDate::where( 'range_id', $Range->id )->delete();
            $Range->delete();
            $this->info( "Deleted {$deleted} RangeDates and Range" );

        }


        //
        //
        //


        $Range = Range::create( $range_data );
        $this->info( 'Created Range' );


        if ( $range_data[ 'appt_only' ] )
        {

            RangeDate::create([
                'range_id'   => $Range->id,
                'start_date' => '2023-01-01',
                'end_date'   => '2099-12-31',
                'price'      => $Range->price,
                'times'      => $Range->times,
                'appt_only'  => true
            ]);

            $this->info( 'Created RangeDate (appt_only)' );

        }


        return 0;

    }

}
