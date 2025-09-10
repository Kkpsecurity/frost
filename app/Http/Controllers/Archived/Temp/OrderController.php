<?php

namespace App\Http\Controllers\Admin\Temp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Classes\Payments\PayFlowProObj;
use App\Classes\Payments\PayPalRESTObj;
use App\Models\Order;
use App\Models\Payments\PayFlowPro;
use App\Traits\PageMetaDataTrait;


class OrderController extends Controller
{

    use PageMetaDataTrait;


    public function index()
    {

        $view = 'admin.temp.orders';
        $content = array_merge([

        ], self::renderPageMeta($view));

        $Orders = Order::whereNotNull( 'completed_at' )
                            ->orderBy( 'completed_at', 'desc' )
                           ->paginate( 50 );

        return view( $view, compact([ 'content', 'Orders' ]) );

    }


    public function Show(Order $Order)
    {

        $view = 'admin.temp.order';
        $content = array_merge([

        ], self::renderPageMeta($view));


        return view( $view, compact([ 'content', 'Order' ]) );

    }


    public function SearchID( Request $Request )
    {

        if ( ! $Request->input( 'search_id' ) )
        {
            return back();
        }

        $search_text = $Request->input( 'search_id' );


        //
        // OrderID
        //

        if ( is_numeric( $search_text ) )
        {
            if ( $Order = Order::firstWhere( 'id', $search_text ) )
            {
                return redirect()->route( 'admin.temp.orders.show', $Order );
            }
            else
            {
                return back()->with( 'warning', 'OrderID not found.' );
            }
        }

        //
        // PayflowPro TransactionID
        //

        if ( preg_match('/^[A-Z0-9]{17}$/', (string) $search_text ) )
        {
            if ( $PayFlowPro = PayFlowPro::firstWhere( 'pp_ppref', $search_text ) )
            {
                return redirect()->route( 'admin.temp.orders.show', $PayFlowPro->Order );
            }
            else
            {
                return back()->with( 'warning', 'PayflowPro Transaction ID not found.' );
            }
        }

        //
        // PayflowPro
        //

        if ( str_ends_with( $search_text, config( 'define.payflowpro.invoice_ext' ) ) )
        {
            if ( $PayFlowPro = PayFlowPro::firstWhere( 'id', str_replace( config('define.payflowpro.invoice_ext'), '', $search_text ) ) )
            {
                return redirect()->route( 'admin.temp.orders.show', $PayFlowPro->Order );
            }
            else
            {
                return back()->with( 'warning', 'PayflowPro ID not found.' );
            }
        }


        //
        // PayflowPro Sandbox
        //

        if ( str_ends_with( $search_text, config('define.payflowpro.sandbox_ext') ) )
        {
            if ( $PayFlowPro = PayFlowPro::firstWhere( 'id', str_replace( config('define.payflowpro.sandbox_ext'), '', $search_text ) ) )
            {
                return redirect()->route( 'admin.temp.orders.show', $PayFlowPro->Order );
            }
            else
            {
                return back()->with( 'warning', 'PayflowPro ID not found.' );
            }
        }


        //
        // no result
        //

        return back()->with( 'warning', 'Search returned no result.' );

    }


    public function SearchName( Request $Request )
    {

        if ( ! $Request->input( 'search_name' ) )
        {
            return back();
        }

        $search_text = $Request->input( 'search_name' );


        $Orders = Order::whereNotNull( 'orders.completed_at' )
                              ->where( 'total_price', '!=', 0.00 )
                               ->join( 'users', 'users.id', '=', 'orders.user_id')
                              ->where( function( $query ) use ( $search_text ) {
                                    $query->where( 'users.fname', 'ILIKE', '%' . $search_text . '%' )
                                        ->orWhere( 'users.lname', 'ILIKE', '%' . $search_text . '%' );
                                })
                            ->orderBy( 'orders.completed_at', 'DESC' )
                                ->get( 'orders.*' )
                           ->paginate( 100 );

        if ( ! $Orders->count() )
        {
            return back()->with( 'warning', 'Search returned no result.' );
        }


        //
        //
        //


        $view = 'admin.temp.orders';
        $content = array_merge([

        ], self::renderPageMeta($view));

        return view( $view, compact([ 'content', 'Orders' ]) );

    }


    public function Refund( Order $Order )
    {

        if ( ! $Payment = $Order->GetPayment() )
        {
            return back()->with( 'error', 'Failed to find Payment' );
        }

        /*
        if ( $Payment->cc_last_result !== 0 )
        {
            return back()->with( 'error', 'Payment not Approved, cannot refund.' );
        }
        */

        if ( $Payment->refunded_at )
        {
            return back()->with( 'error', 'Payment Already Refunded.' );
        }

        if ( ! $Payment->IsCompleted() )
        {
            return back()->with( 'error', 'Payment not Completed, cannot refund.' );
        }


        ( new PayFlowProObj( $Payment ) )->IssueRefund();

        return back()->with( 'success', 'Payment Successfully Refunded' );

    }


    public function ReactivateCourseAuth( Order $Order )
    {

        if ( ! $Order->course_auth_id )
        {
            return back()->with( 'error', 'Order has no CourseAuth' );
        }

        if ( ! $Order->CourseAuth->disabled_at )
        {
            return back()->with( 'error', 'CourseAuth was not disabled' );
        }

        $Order->CourseAuth->update([
            'disabled_at'     => null,
            'disabled_reason' => null,
        ]);

        return back()->with( 'success', 'CourseAuth Reenabled' );

    }


    public function GetSaleDetails( Order $Order )
    {

        if ( ! $Payment = $Order->GetPayment() )
        {
            logger( 'Failed to find Payment' );
            return back()->with( 'error', 'Failed to find Payment' );
        }

        ( new PayPalRESTObj( $Payment ) )->GetSaleDetails();

        return back()->with( 'success', 'Payment Updated' );

    }


    public function GetMissingOrderDetails()
    {

        $Payments = PayFlowPro::whereNotNull( 'completed_at' )
                                 ->whereNull( 'cc_transtime' )
                                  ->whereNot( 'pp_is_sandbox' )
                                     ->where( 'cc_last_result', 0 )
                                       ->get();

        foreach ( $Payments as $Payment )
        {
            ( new PayPalRESTObj( $Payment ) )->GetSaleDetails();
        }

        return back();

    }


    //
    //
    //


    public function CSVQuery()
    {
        $view = 'admin.temp.csvquery';
        $content = array_merge([

        ], self::renderPageMeta($view));

        return view( $view, compact([ 'content' ]) );

    }


    public function CSVDump( Request $Request ) : void
    {

        $Query = Order::whereNotNull( 'completed_at' )
                           ->orderBy( 'completed_at' )
                              ->with( 'User' )
                              ->with( 'Course' );

        if ( $Request->input( 'start_date' ) )
        {
            $Query->where( 'completed_at', '>=', $Request->input( 'start_date' ) );
        }

        if ( $Request->input( 'end_date' ) )
        {
            $Query->where( 'completed_at', '<=', $Request->input( 'end_date' ) );
        }

        if ( $Request->input( 'course_id' ) )
        {
            $Query->where( 'course_id', $Request->input( 'course_id' ) );
        }


        //
        // create CSV
        //

        $csv = fopen( 'php://memory', 'w' );

        fputcsv( $csv, [ 'OrderID', 'Completed At', 'Refunded At', 'Student Name', 'Course' ] );

        foreach ( $Query->get() as $Order )
        {

            fputcsv( $csv, [
                $Order->id,
                $Order->CompletedAt(),
                $Order->RefundedAt(),
                $Order->User->fullname(),
                $Order->Course->ShortTitle(),
            ] );

        }


        //
        // send it
        //

        fseek( $csv, 0 );
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="FROST Query.csv";' );
        fpassthru( $csv );
        exit();

    }


}
