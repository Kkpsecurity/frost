<?php

namespace KKP\Laravel\ModelTraits;


trait TogglesBooleans
{

    /**
     * Toggles boolean value (saves record)
     *   SQL throws exception on invalid field_name
     *
     * @param   string  $field_name
     * @return  void
     */
    public function toggle( string $field_name ) : void
    {

        $this->setAttribute( $field_name, ! $this->getAttribute( $field_name ) )
             ->save();

    }

}
