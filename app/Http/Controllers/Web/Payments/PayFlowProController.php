<?php

namespace App\Http\Controllers\Web\Payments;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;

use App\Classes\Payments\PayFlowProObj;
use App\Classes\Payments\PayPalHelpersTrait;
use App\Models\CourseAuth;
use App\Models\Payments\PayFlowPro;
use KKP\Laravel\PgTk;


class PayFlowProController extends Controller
{


    use PageMetaDataTrait;


    public function index( Request $Request, PayFlowPro $PayFlowPro )
    {

        $view = 'frontend.payments.payflowpro';

        $content = self::renderPageMeta( $view );


        $Order         = $PayFlowPro->Order;
        $Course        = $PayFlowPro->Order->GetCourse();
        $payflow_route = ( new PayFlowProObj( $PayFlowPro ) )->PayFlowLinkURL();
        $return_route  = route( 'payments.payflowpro', $PayFlowPro );
        $token_route   = route( 'payments.payflowpro.get_token', $PayFlowPro );

        return view( $view, compact([ 'content', 'Course', 'Order', 'PayFlowPro', 'payflow_route', 'return_route', 'token_route' ]) );

    }


    public function HandleReturn( Request $Request, PayFlowPro $PayFlowPro ) : RedirectResponse
    {

        //
        // validate source
        //


        #if ( ! preg_match( '/\.paypal\.com/', $Request->server( 'HTTP_REFERER' ) ) )
        #{
        #    return redirect( '/' )->with( 'error', 'Invalid HTTP_REFERER' );
        #}

        if ( ! $Request->has( 'RESULT' ) )
        {
            return redirect( '/' )->with( 'error', 'Invalid RESULT' );
        }


        //
        // need to log in user?
        //   TODO: revisit this
        //

        if ( ! Auth::check() )
        {
            Auth::login( $PayFlowPro->Order->GetUser() );
        }


        //
        // save initial response
        //


        $timestamp = PgTk::now(); // use the same for all records

        $PayFlowPro->update([

            'cc_last_at'        => $timestamp,
            'cc_last_result'    => $Request->input( 'RESULT'  ),
            'cc_last_respmsg'   => $Request->input( 'RESPMSG' ),
            'cc_last_data'      => json_encode( $_POST ),  // don't trust $Response->all()

        ]);


        //
        // payment failed
        //


        if ( $Request->input( 'RESULT' ) !== '0' )
        {

            $payment_error = $PayFlowPro::TransactionError( (int) $Request->input( 'RESULT' ) );

            logger( "Payment Error: {$payment_error} -- OrderID: {$PayFlowPro->Order->id} -- User: {$PayFlowPro->Order->GetUser()}" );

            return redirect()->route( 'payments.payflowpro', $PayFlowPro )
                              ->with( 'error', $payment_error );

        }


        //
        // success
        //   update Payment
        //


        $PayFlowPro->ResetTokenVars(); // doesn't save

        $PayFlowPro->completed_at  = $timestamp;
        $PayFlowPro->pp_pnref      = $Request->input( 'PNREF' );
        $PayFlowPro->pp_ppref      = $Request->input( 'PPREF' );

        if ( $Request->has( 'AMT' ) )
        {
            $PayFlowPro->cc_amount = $Request->input( 'AMT' );
        }

        if ( $Request->has( 'TRANSTIME' ) )
        {
            $PayFlowPro->cc_transtime = $PayFlowPro::Date_to_UTC( $Request->input( 'TRANSTIME' ) );
        }

        $PayFlowPro->save();


        //
        // complete Order
        //


        $PayFlowPro->Order->update([
            'payment_type_id' => $PayFlowPro->PaymentTypeID()
        ]);

        $PayFlowPro->Order->SetCompleted();


        //
        // dispatch job(s)
        //


        $PayFlowPro->DispatchGetSaleDetails();

        // TODO: email notification
        // TODO: ? user notification


        //
        // fin
        //

        return redirect()->route( 'order.completed', $PayFlowPro->Order );

    }



    #########################
    ###                   ###
    ###   AJAX requests   ###
    ###                   ###
    #########################


    public function GetToken( PayFlowPro $PayFlowPro ) : JsonResponse
    {

        abort_unless( $PayFlowPro->id, 500, 'Missing PayFlowPro' );


        $PayFlowProObj = new PayFlowProObj( $PayFlowPro );

        if ( ! $PayFlowProObj->TokenIsValid() )
        {
            $PayFlowProObj->SetToken(); // updates $PayFlowPro
        }
        else
        {
            $PayFlowPro->increment( 'pp_token_count' );
        }


        return response()->json([

            'SECURETOKENID' => $PayFlowPro->pp_token_id,
            'SECURETOKEN'   => $PayFlowPro->pp_token,

        ]);

    }


}
