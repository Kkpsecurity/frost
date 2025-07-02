<?php
declare(strict_types=1);

namespace App\Classes\Payments;



trait PayFlowProTrait
{


    public function PayFlowProURL() : string
    {
        return 'https://' . ( $this->_Payment->pp_is_sandbox ? 'pilot-payflowpro' : 'payflowpro' ) . '.paypal.com';
    }

    public function PayFlowLinkURL() : string
    {
        return 'https://' . ( $this->_Payment->pp_is_sandbox ? 'pilot-payflowlink' : 'payflowlink' ) . '.paypal.com';
    }


    public function GenTokenID() : string
    {

        $token_len = 36;
        $chars     = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str       = '';

        while ( strlen( $str ) < $token_len )
        {
            $str .= $chars[ rand( 0, 61 ) ];
        }

        return $str;

    }


    public static function TransactionError( int $result_code ) : string
    {
        switch( $result_code )
        {

            case 0:
                return '';  // Approved
                break;

            case 12:
                return 'Card Declined';
                break;

            case 13:
                return 'Referral: Please contact your banking institution.';
                break;

            case 25:
                return 'Processor does not accept this type of card.';
                break;

            case 23:
                return 'Invalid card number.';
                break;
            case 24:
                return 'Invalid expiration date.';
                break;
            case 114:
                return 'Invalid security code.';
                break;

            case 30:
                return 'Processor Error: Duplicate transaction.';
                break;

            case 160:
                return 'Processor Error: Security Token Reuse. Please try again.';
                break;

            default:
                return "Unknown Processor Error (Code {$result_code})";

        }
    }

}
