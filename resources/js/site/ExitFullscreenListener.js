/*  begin ExitFullscreenListener.js  */
window.addEventListener( 'message', (event) => {

    if ( event.data == 'exitFullscreen' )
    {

        if ( ! document.fullscreenElement )
        {
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

    }

}, false );
/*  end ExitFullscreenListener.js  */
