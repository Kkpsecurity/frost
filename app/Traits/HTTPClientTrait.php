<?php

namespace App\Traits;

use Illuminate\Http\Client\Response as HTTPResponse;


trait HTTPClientTrait
{


    public static function ResponseContent(HTTPResponse $Response)
    {
        return $Response->getBody()->getContents();
    }


    public static function ResponseIsJSON(HTTPResponse $Response): bool
    {
        return strpos($Response->header('Content-Type'), 'application/json') === false ? false : true;
    }
}
