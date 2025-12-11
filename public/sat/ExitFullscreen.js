//
// window.parent.postMessage( 'exitFullscreen', '*' );
//


$(document).ready(function() {

    console.log( '***  exitFullscreen loaded  ***' );

});


window.addEventListener( 'message', (event) => {


    console.log( '***  exitFullscreen received message: ' + event.data + '  ***' );

    if ( event.data == 'exitFullscreen' )
    {

        if ( ! document.fullscreenElement )
        {
            console.log( '***  Not in fullscreen  ***' );
            return;
        }

        console.log( '***  Attempting exitFullscreen()  ***' );

        if ( document.exitFullscreen )
        {
            document.exitFullscreen();
        }
        else if ( document.webkitExitFullscreen )
        {
            document.webkitExitFullscreen();
        }
        else if ( document.msExitFullscreen )
        {
            document.msExitFullscreen();
        }
        else
        {
            alert( 'No exitFullscreen() method available.' );
        }

    }


}, false );
