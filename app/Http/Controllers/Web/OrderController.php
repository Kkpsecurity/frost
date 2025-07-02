<?php

namespace App\Http\Controllers\Web;

use Auth;
use Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\RCache;
use App\Models\Order;
use App\Traits\PageMetaDataTrait;


class OrderController extends Controller
{

    use PageMetaDataTrait;


    public function ShowCompleted( Order $Order )
    {

        $view = 'frontend.payments.payment_success';

        $content = self::renderPageMeta( $view );

        return view( $view, compact([ 'content', 'Order' ]) );

    }


    public function ApplyDiscountCode( Request $Request, Order $Order ) : RedirectResponse
    {

        if ( $Order->completed_at )
        {
            return back()->with( 'error', 'Order already completed' );
        }

        if ( ! $Request->has( 'discount_code' ) or $Request->input( 'discount_code' ) == '' )
        {
            return back();
        }

        if ( $error = $Order->ApplyDiscountCode( $Request->input( 'discount_code' ) ) )
        {
            return back()->withErrors( Validator::make([],[])->errors()->add( 'discount_code', $error ) )
                         ->withInput();
        }


        if ( $Order->DiscountCode->AppliesFree() )
        {
            $Order->SetCompleted();
            return redirect()->route( 'order.completed', $Order );
        }


        return back();

    }


}
