<?php

namespace App\Providers;

#use Illuminate\Support\Facades\Request;
#use Illuminate\Support\Facades\Session;
#use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {

        //
        // Global view composer
        //

        /*
        View::composer( '*', function ( $view ) {
        });
        */


        //
        // Messages view composer
        //

        /*

        View::composer( 'messages.messages', function ( $view ) {

            //
            // flash messages
            //

            // error->danger in _variables.scss
            $alert_types = [ 'success', 'error', 'danger', 'warning', 'info',
                             'primary', 'secondary', 'light', 'dark' ];

            $messages = [];

            foreach ( $alert_types as $alert_type )
            {
                if ( $message = Session::get( $alert_type ) )
                {
                    $messages[ $alert_type ] = $message;
                }
            }

            View::share( 'messages', $messages );


            //
            // validation errors
            //

            $handled_errors = (array) ( $view->handled_errors ?? [] );
            $display_errors = [];

            foreach ( $view->errors->getMessages() as $key => $messages )
            {
                if ( ! in_array( $key, $handled_errors ) )
                {
                    $display_errors = array_merge( $display_errors, $messages );
                }
            }

            View::share( 'display_errors', $display_errors );

        });

        */

    }

}
