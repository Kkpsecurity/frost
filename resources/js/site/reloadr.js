/*

MIT No Attribution

Copyright 2023 Chris Jones <jonesy@crimsonshade.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/


//
// update this for vite:
// export class reloadr
//
class reloadr
{

    init_mtime = 0;
    url;
    delay;

    constructor( url, delay )
    {

        this.url   = url;
        this.delay = delay;

        //
        // run once
        //

        this.getMTime();

        //
        // set interval
        //

        let _self = this;

        setInterval(() => { _self.getMTime(); }, delay * 1000 );

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
                        // first run; set init_mtime
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

window.reloadr = reloadr;
