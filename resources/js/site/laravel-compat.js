/*  begin laravel-compat.js  */

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
    }
});


/**
 *
 * public functions
 *
 */


/* exported PostRoute */
function PostRoute( route, formfields )
{

    let FORM = $( '<form />', { 'method': 'POST', 'action': route, 'style': 'display: none;' })
                .appendCSRF();

    $.each( formfields, function( key, val ) {
        FORM.append( $( '<input />' ).attr({ name: key, value: val }) );
    });

    FORM.appendTo( $( 'body' ) ).submit();

}


/**
 *
 * jQuery closures
 *
 */


(function ( $ ) {

    /**
     *
     * function: appendCSRF()
     *   appends CSRF token to form
     *
     * usage:
     *   $( '#FORM' ).appendCSRF();
     *
     */

    $.fn.appendCSRF = function() {

        if ( ! $( this ).is( 'form' ) )
        {
            alert( 'Attempted to $.appendCSRF to !FORM' );
            return;
        }

        $( this ).append(
            $( '<input />', {
                type:  'hidden',
                name:  '_token',
                value: $( 'meta[name="csrf-token"]' ).attr( 'content' )
            })
        );

        return $( this );

    };

}( jQuery ));


/**
 *
 * activate items by class
 *   most of these are not laravel-specific
 *   but I'm putting them all in one place
 *
 */


$(document).ready(function() {


    $( '.goIndex' ).click(function() {

        location.href = ( typeof route_home !== 'undefined' ? route_home : '/' );

    });

    $( '.goBack' ).click(function() { history.back(); });


    $( '.doLogout' ).click(function(e) {

        e.preventDefault();
        PostRoute( '/logout' );

    });


    $( '.doRoute' ).click(function () {

        if ( ! $( this ).data( 'route' ) )
        {
            alert( 'This button has an undefined or empty route!' );
            return;
        }

        location.href = $( this ).data( 'route' );

    });


    $( '.doNewTab' ).click(function () {

        if ( ! $( this ).data( 'route' ) )
        {
            alert( 'This button has an undefined or empty route!' );
            return;
        }

        window.open( $( this ).data( 'route' ), '_blank' );

    });


    $( '.doRouteSelect' ).change(function() {

        if ( $( this ).val() )
        {
            location.href = $( this ).val();
        }

    });



    $( '.btnRouteSubmit' ).click(function (e) {

        e.preventDefault();

        if ( ! $( this ).data( 'route' ) )
        {
            alert( 'Missing data-route' );
            return false;
        }


        //
        // no existing form required
        //

        if ( ! $( this ).data( 'form_id' ) )
        {
            PostRoute( $( this ).data( 'route' ) );
            return;
        }


        //
        // use existing form
        //

        let form  = $( '#' + $( this ).data( 'form_id' ) );

        form.attr( 'action', $( this ).data( 'route' ) );

        if ( $( this ).attr( 'type' ).toLowerCase() == 'submit' )
        {
            // trigger 'real' submit
            let btn = $( '<button type="submit" style="display: none;" />' );
            btn.appendTo( form ).trigger( 'click' );
        }
        else
        {
            // skip form validation
            form.trigger( 'submit' );
        }

    });


    $( '.btnConfirm' ).click(function () {


        if ( $( this ).data( 'confirm_text' ) )
        {
            return confirm( $( this ).data( 'confirm_text' ) );
        }

        return confirm( 'Really ' + $( this ).contents().first().text() + ' ?' );

    });


});
/*  end laravel-compat.js  */
