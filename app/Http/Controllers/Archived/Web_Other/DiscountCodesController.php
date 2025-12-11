<?php

namespace App\Http\Controllers\Web\Other;

use App;
use Auth;
use Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

use App\Models\DiscountCode;
use App\Models\Order;
use KKP\TextTk;


class DiscountCodesController extends Controller
{


    public const SESSION_KEY = 'discount_code_usage';


    public function Usage( Request $Request, DiscountCode $DiscountCode ) : View
    {

        if ( ! $this->_ValidateSession( $Request, $DiscountCode ) )
        {
            return view( 'other.discount_code_usage.auth', compact([ 'DiscountCode' ]) );
        }

        $Orders = $this->_Orders( $DiscountCode );

        return view( 'other.discount_code_usage.usage', compact([ 'DiscountCode', 'Orders' ]) );

    }



    public function UsageCSV( Request $Request, DiscountCode $DiscountCode )
    {

        if ( ! $this->_ValidateSession( $Request, $DiscountCode ) )
        {
            return view( 'other.discount_code_usage.auth', compact([ 'DiscountCode' ]) );
        }


        if ( class_exists( '\Debugbar' ) ) { \Debugbar::disable(); }


        //
        // HTTP headers
        //


        ob_clean();
        header( 'Pragma: public' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Cache-Control: private', false );
        header( 'Content-Type: text/csv');
        header( 'Content-Disposition: attachment; filename=discount_code_usage.csv' );


        //
        // CSV data
        //


        $csv_out = fopen( 'php://output', 'w' );

        fputcsv( $csv_out, [ 'Student', 'Email', 'Order Completed', 'Course Started', 'Course Completed', 'Final Status' ] );

        foreach ( $this->_Orders( $DiscountCode ) as $Order )
        {

            //
            // NOTE: there is *always* a CourseAuth for these Orders
            //

            fputcsv( $csv_out, [
                $Order->User->fullname(),
                $Order->User->email,
                $this->_FormatTimestamp( $Order->completed_at ),
                $Order->CourseAuth->StartedAt(),
                $this->_FormatTimestamp( $Order->CourseAuth->completed_at ),
                $Order->CourseAuth->FinalStatus( false ),
            ]);

        }


        //
        // send it
        //


        fclose( $csv_out );
        ob_flush();
        exit();

    }


    public function AuthCode( Request $Request, DiscountCode $DiscountCode ) : RedirectResponse
    {

        if ( $DiscountCode->code === TextTk::Sanitize( $Request->input( 'discount_code' ) ) )
        {
            Session::put([ self::SESSION_KEY => $DiscountCode->code ]);
            return back();
        }

        return back()->withErrors([ 'discount_code' => 'Invalid Discount Code' ]);

    }


    public function ForgetCode() : RedirectResponse
    {
        Session::forget( self::SESSION_KEY );
        return back();
    }


    //
    //
    //


    private function _ValidateSession( Request $Request, DiscountCode $DiscountCode ) : bool
    {

        if ( Auth::check() && Auth::user()->IsAnyAdmin() )
        {
            return true;
        }

        if ( App::environment( 'local' ) )
        {
            $Request->merge([ 'discount_code' => $DiscountCode->code ]);
        }

        return Session::get( self::SESSION_KEY ) === $DiscountCode->code;

    }


    private function _Orders( DiscountCode $DiscountCode ) : Collection
    {

        return Order::where( 'discount_code_id', $DiscountCode->id )
                  ->orderBy( 'completed_at', 'DESC' )
                     ->with( 'CourseAuth' )
                     ->with( 'CourseAuth.StudentUnits' )
                     ->with( 'User' )
                      ->get();

    }


    private function _FormatTimestamp( ?int $timestamp ) : string
    {

        if ( ! $timestamp ) { return ''; }

        return Carbon::parse( $timestamp )
                        ->tz( 'America/New_York' )
                 ->isoFormat( 'YYYY-MM-DD HH:mm:ss' );

    }


}
