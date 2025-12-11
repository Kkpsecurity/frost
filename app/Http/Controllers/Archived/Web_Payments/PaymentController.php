<?php namespace App\Http\Controllers\Web\Payments;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Traits\PageMetaDataTrait;

class PaymentController extends Controller {

    use PageMetaDataTrait;

    public function payments($item_id)
    {
       
        $content = array_merge([
            'item_id' => $item_id
        ], self::renderPageMeta('payments'));

        return redirect('payments/paypal/payflowpro/' . $item_id);
        // return view('frontend.payments.payments', compact('content'));
    }

    public function payflowpro($item_id)
    {

        $item = Course::where('id', $item_id)->first(); 

        $content = array_merge([
            'item_id' => $item_id
        ], self::renderPageMeta('paymens/payflowpros'));

        return view('frontend.payments.payflowpro', compact('content'));
    }




}
