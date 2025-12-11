<?php

namespace App\Http\Controllers\Admin\Temp;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use App\Models\Range;
use App\Models\RangeDate;
use KKP\Laravel\PgTk;


class RangeController extends Controller
{

    use PageMetaDataTrait;


    public function index()
    {

        $view    = 'admin.temp.ranges';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        $Ranges = Range::all()
                       ->where( 'id', '!=', -1 )
                       ->sortBy( 'city' )
                       ->sortByDesc( 'is_active' );

        return view( $view, compact([ 'content', 'Ranges' ]) );

    }


    public function Create()
    {

        $view    = 'admin.temp.range_create';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        return view( $view, compact( 'content' ) );

    }


    public function Show( Range $Range )
    {

        $view    = 'admin.temp.range';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        return view( $view, compact([ 'content', 'Range' ]) );

    }


    public function RangeInfo( Range $Range )
    {

        $view    = 'admin.temp.range_info';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        return view( $view, compact([ 'content', 'Range' ]) );

    }


    public function ShowDates( Range $Range )
    {

        $view    = 'admin.temp.range_dates';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $RangeDates = RangeDate::where( 'range_id', $Range->id )
                    ->orderByDesc( 'start_date' )
                    ->get();

        $CourseAuths = collect([]);
        foreach ( $RangeDates as $RangeDate )
        {
            $CourseAuths->put( $RangeDate->id, $RangeDate->CourseAuths()->with( 'User' )->get() );
        }

        return view( $view, compact([ 'content', 'Range', 'RangeDates', 'CourseAuths' ]) );

    }


    //
    // create / update records
    //


    public function Store( Request $Request )
    {

        if ( $Request->input( 'appt_only' ) )
        {
            $appt_only = true;
            $times     = 'By Appointment Only';
        }
        else
        {
            $appt_only = false;
            $times     = $Request->input( 'times' );
        }


        $Range = Range::create([

            'name'          => $Request->input( 'name' ),
            'city'          => $Request->input( 'city' ),
            'address'       => $Request->input( 'address' ),
            'inst_name'     => $Request->input( 'inst_name'  ),
            'inst_email'    => $Request->input( 'inst_email' ),
            'inst_phone'    => $Request->input( 'inst_phone' ),
            'price'         => $Request->input( 'price' ),
            'times'         => $times,
            'appt_only'     => $appt_only,
            'range_html'    => $Request->input( 'range_html' ),

        ]);


        if ( $appt_only )
        {
            RangeDate::create([
                'range_id'   => $Range->id,
                'start_date' => '2023-01-01',
                'end_date'   => '2099-12-31',
                'price'      => $Range->price,
                'times'      => $times,
                'appt_only'  => true
            ]);
        }


        return redirect()->route( 'admin.temp.ranges.show', $Range );

    }


    public function Update( Request $Request, Range $Range )
    {

        $Range->update([

            'name'          => $Request->input( 'name' ),
            'city'          => $Request->input( 'city' ),
            'address'       => $Request->input( 'address' ),
            'inst_name'     => $Request->input( 'inst_name'  ),
            'inst_email'    => $Request->input( 'inst_email' ),
            'inst_phone'    => $Request->input( 'inst_phone' ),
            // no times
            // no price

        ]);

        return back()->with( 'success', 'Updated' );

    }


    public function UpdateTimes( Request $Request, Range $Range )
    {

        $new_times = $Request->input( 'times' );

        $Range->update([ 'times' => $new_times ]);

        $updated = RangeDate::where( 'range_id', $Range->id )
                            ->where( 'start_date', '>', Carbon::now() )
                           ->update([ 'times' => $new_times ]);

        return back()->with( 'info', "Updated {$updated} RangeDates to '{$new_times}'" );

    }


    public function UpdatePrice( Request $Request, Range $Range )
    {

        $new_price = $Request->input( 'price' );

        $Range->update([ 'price' => $new_price ]);

        $updated = RangeDate::where( 'range_id', $Range->id )
                            ->where( 'start_date', '>', Carbon::now() )
                           ->update([ 'price' => $new_price ]);

        return back()->with( 'info', "Updated {$updated} RangeDates to \${$new_price}" );

    }


    public function UpdateRangeHTML( Request $Request, Range $Range )
    {

        $Range->range_html = $Request->input( 'range_html' );
        $Range->save();

        return back()->with( 'success', 'Updated' );

    }


    public function ToggleRangeActive( Range $Range )
    {

        $Range->toggle( 'is_active' );

        if ( $Range->is_active )
        {
            return back()->with( 'success', 'Reactivated' );
        }

        return back()->with( 'warning', 'Deactivated' );

    }


    //
    // RangeDates
    //


    public function AddDates( Request $Request, Range $Range )
    {

        $added = 0;

        foreach ( range( 0, ( $Request->input( 'max_records' ) - 1 ) ) as $idx )
        {
            if ( $Request->input( "start_date_{$idx}" ) )
            {

                RangeDate::create([
                    'range_id'      => $Range->id,
                    'start_date'    => $Request->input( "start_date_{$idx}" ),
                    'end_date'      => $Request->input( "end_date_{$idx}" ),
                    'times'         => $Request->input( "times_{$idx}" ),
                    'price'         => $Request->input( "price_{$idx}" ),
                ]);

                $added++;

            }
        }

        return back()->with( 'success', "Added {$added} Range Dates" );

    }



    public function ToggleRangeDateActive( RangeDate $RangeDate )
    {

        $RangeDate->toggle( 'is_active' );

        if ( $RangeDate->is_active )
        {
            return back()->with( 'success', 'Reactivated' );
        }

        return back()->with( 'warning', 'Deactivated' );

    }


}
