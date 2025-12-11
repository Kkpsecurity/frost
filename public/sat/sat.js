window.addEventListener( 'load', function() {


    // $( '[data-bs-toggle="tooltip"]' ).tooltip();

    $( '.copy_to_clipboard' ).click(function() {
        copy_to_clipboard( $(this) );
    });


    $( '#range_date_id' ).change(function() {

        range = ranges[ $( this ).find( 'option:selected' ).data( 'range_id' ) ];

        $( '#times'      ).text( range.times );
        $( '#price'      ).text( range.price );
        $( '#address'    ).html( range.address );
        $( '#range_html' ).html( range.range_html );

    }).trigger( 'change' );



});




function copy_to_clipboard( elem )
{

    //
    // create hidden input field to select
    //

    let tempInput = $( '<input />' ).attr({
        style: 'position: absolute; z-index: -9999; opacity: 0;',
        value: elem.text(),
    })
    .appendTo( $( 'body' ) )
    .select();

    //
    // attempt copy
    //

    let notifyMsg = 'Copied to clipboard';
    let className = 'info';

    if ( ! document.execCommand( 'copy' ) )
    {
        alert( 'Copy to clipboard Failed' );
        return;
        // notifyMsg = 'Copy to clipboard Failed';
        // className = 'error';
    }

    //
    // remove hidden input
    //

    tempInput.remove();


    //
    // display feedback
    //

    elem.animate( { opacity: 0.2  }, { duration: 250 } )
        .animate( { opacity: 1.0  }, { duration: 250 } );

    /*
    elem.notify( notifyMsg, {
        className: className,
        autoHideDelay: 750,
        showDuration:  250,
        hideDuration:  250
    });
    */


}

/*  end copy_to_clipboard.js  */
