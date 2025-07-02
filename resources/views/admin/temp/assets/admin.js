window.addEventListener( 'load', function() {


    $( '.copy_to_clipboard' ).click(function() {
        copy_to_clipboard( $(this) );
    });


    $( '.confirmThis' ).click( function() {
        if ( confirm( 'Are you sure you want to ' + $( this ).val() + '?' ) )
        {
            $( this ).closest( 'form' ).submit();
        }
    });


    //
    // formatters
    //


    $( '.cleave-int' ).each(function() {
        new Cleave( $( this ), {
            numeral:   true,
            numeralPositiveOnly: true,
            delimiter: ''
        });
    });

    $( '.cleave-float' ).each(function() {
        new Cleave( $( this ), {
            numeral:   true,
            delimiter: '.'
        });
    });

    $( '.cleave-date' ).each(function() {
        new Cleave( $( this ), {
            date: true,
            delimiter: '-',
            datePattern: [ 'Y', 'm', 'd' ]
        });
    });

    $( '.cleave-price' ).each(function() {
        new Cleave( $( this ), {
            numeral: true,
            numeralDecimalScale: 2,
            numeralPositiveOnly: true,
            numeralThousandsGroupStyle: 'none',
            delimiter: '.'
        });
    });


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

}
