<?php

namespace KKP\Laravel\HTTPClient;

use Auth;
use Exception;
use stdClass;


class HTTPClientErrorParams
{


    public string $facility;

    public ?string $action;
    public ?int    $user_id;
    public ?array  $form_params;


    public function __construct( string $facility, array|stdclass $params = null )
    {

        $this->facility = $facility;

        if ( $params )
        {
            $this->Update( $params );
        }

    }


    public function Update( array|stdclass $params ) : self
    {

        foreach ( $params as $name => $value )
        {
            if ( ! property_exists( $this, $name ) )
            {
                throw new Exception( __CLASS__ . " has no property '{$name}'" );
            }
            else
            {
                $this->$name = $value;
            }
        }

        return $this;

    }


    public function Reset() : self
    {

        $this->action      = null;
        $this->user_id     = null;
        $this->form_params = null;

        return $this;

    }


}
