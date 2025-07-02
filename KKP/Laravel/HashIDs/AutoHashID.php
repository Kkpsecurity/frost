<?php

namespace KKP\Laravel\HashIDs;

use Exception;
use Illuminate\Database\Eloquent\Model;

use KKP\Laravel\HashIDs\HashID;
use KKP\Laravel\HashIDs\BasicHashID;


trait AutoHashID
{

    use BasicHashID; // inject hash_id()


    /**
     * Get the value of the model's route key [ as hash_id ]
     *
     * @return  string|null  (hash_id)
     */
    public function getRouteKey() : ?string
    {

        if ( $value = parent::getRouteKey() )
        {
            $model_field = get_class( $this ) . '->' . parent::getRouteKeyName();
            return HashID::Validate_Encode( $value, $model_field );
        }

        return null;

    }


    /**
     * Retrieve the model for a bound value [ from hash_id ]
     *
     * @param   mixed        $value  (hash_id)
     * @param   string|null  $field
     * @return  \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding( $value, $field = null ) : ?Model
    {

        $logprefix = get_class( $this ) . '::' . __FUNCTION__ . "({$value}, {$field}) [AutoHashID]";


        if ( ! filter_var( $value, FILTER_VALIDATE_INT ) )
        {

            kkpdebug( 'Router', $logprefix . ' not an integer' );
            return null;

        }

        if ( property_exists( $this, 'modelcache' ) )
        {

            kkpdebug( 'Router', $logprefix . ' returning from BCache' );
            return ($this->modelcache)::find( HashID::decode( $value ) );

        }
        else
        {

            kkpdebug( 'Router', $logprefix . ' returning from Database' );

            return $this->firstWhere(
                ( $field ?: $this->getRouteKeyName() ),
                HashID::decode( $value )
            );

        }

    }


}
