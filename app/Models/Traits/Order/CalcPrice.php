<?php
declare(strict_types=1);

namespace App\Models\Traits\Order;


trait CalcPrice
{


    private function _CalcPrice() : void
    {

        //
        // apply DiscountCode
        //

        if ( $DiscountCode = $this->GetDiscountCode() )
        {

            if ( $DiscountCode->set_price )
            {

                $total_price = $DiscountCode->set_price;

                kkpdebug( 'OrderCalc', "set_price: {$total_price}" );

            }
            else // percent
            {

                $total_price = $this->course_price
                           - ( $this->course_price * ( $DiscountCode->percent / 100 ) );

                kkpdebug( 'OrderCalc', "discounted: {$total_price}" );

            }

            $this->total_price = $total_price;

        }
        else
        {

            $this->total_price = $this->course_price;

            kkpdebug( 'OrderCalc', 'No Discount Code' );

        }

        //
        // update Order
        //

        $this->update([ 'total_price' => $total_price ]);

    }


}
