// export class reloadr
class reloadr
{

    init_mtime = 0;
    url;
    seconds;

    constructor( url, seconds )
    {

        this.url     = url;
        this.seconds = seconds;

        //
        // run once
        //

        this.getMTime();

        //
        // set interval
        //

        let _self = this;

        setInterval(() => { _self.getMTime(); }, seconds * 1000 );

    }

    getMTime()
    {

        let _self = this;

        let xhr = new XMLHttpRequest();

        xhr.open( 'HEAD', _self.url, true );

        xhr.onerror = function()
        {
            console.log( 'reloadr: Failed to HEAD ' + url );
        };

        xhr.onreadystatechange = function()
        {
            if ( this.readyState == 2 )
            {
                if ( this.getResponseHeader( 'Last-Modified' ) )
                {

                    // convert to UNIX timestamp
                    let remote_mtime = Date.parse( this.getResponseHeader( 'Last-Modified' ) );

                    if ( ! _self.init_mtime )
                    {
                        // first invocation
                        _self.init_mtime = remote_mtime;
                    }
                    else if ( _self.init_mtime != remote_mtime )
                    {
                        location.reload();
                    }

                }
            }
        };

        xhr.send();

    }

}

// window.reloadr = reloadr;
