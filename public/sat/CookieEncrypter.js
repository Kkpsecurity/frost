/*  begin CookieEncrypter.js  */
CookieEncrypter = {

    cookie_key:      null,
    cookie_path:     null,
    cookie_sameSite: 'strict',
    cookie_secure:   true,
    csrf_token:      null,
    data_prefix:     'CookieEncrypter|',


    init: function( cookie_key, cookie_path )
    {

        if ( cookie_key.length  < 3 ) { console.log( 'CookieEncrypter: cookie_key too short (min: 3)'  ); return; }
        if ( cookie_path.length < 3 ) { console.log( 'CookieEncrypter: cookie_path too short (min: 3)' ); return; }

        this.cookie_key  = cookie_key;
        this.cookie_path = cookie_path;
        this.csrf_token  = document.querySelector('meta[name="csrf-token"]').content;

        if ( ! this.csrf_token )
        {
            console.log( 'CookieEncrypter: Failed to get csrf_token' );
            return;
        }

    },


    setCookie: function( cookie_exp_sec, data )
    {

        if ( ! this.csrf_token )
        {
            return { error: 'CookieEncrypter not initialized' };
        }

        const msg_prefix = 'CookieEncrypter.setCookie(): ';

        try {

            //
            // prepare vars
            //

            const encrypted = CryptoJS.AES.encrypt(
                                    this.data_prefix + JSON.stringify( data ),
                                    this.cookie_path + this.csrf_token
                              ).toString();

            let expiration = new Date();
            expiration.setTime( expiration.getTime() + ( cookie_exp_sec * 1000 ) );

            const attributes = {
                expires:  expiration,
                path:     this.cookie_path,
                sameSite: this.cookie_sameSite,
                secure:   this.cookie_secure,
            };

            //
            // set cookie
            //

            Cookies.set( this.cookie_key, encrypted, attributes );

            return { success: msg_prefix + 'Success' };

        } catch (e) {

            console.log(e);
            return { error: msg_prefix + 'FAILED' };

        }

    },


    getCookie: function()
    {

        if ( ! this.csrf_token )
        {
            return { error: 'CookieEncrypter not initialized' };
        }

        const msg_prefix = 'CookieEncrypter.getCookie(): ';

        let encrypted;
        let decrypted;

        //
        // get cookie
        //

        try {

            encrypted = Cookies.get( this.cookie_key );

            if ( ! encrypted )
            {
                return { error: msg_prefix + 'FAILED TO GET COOKIE [retrieve]' };
            }

        } catch (e) {

            console.log(e);
            return { error: msg_prefix + 'FAILED TO GET COOKIE [exception]' };

        }

        //
        // decrypt
        //

        const regexp = new RegExp( '^' + this.quoteRegex( this.data_prefix ) );

        try {

            // this can throw an exception on bad decryption
            decrypted = CryptoJS.AES.decrypt( encrypted, this.cookie_path + this.csrf_token ).toString( CryptoJS.enc.Utf8 );

            if ( ! decrypted )
            {
                return { error: msg_prefix + 'FAILED TO DECRYPT [empty]' };
            }

            if ( ! decrypted.match( regexp ) )
            {
                return { error: msg_prefix + 'FAILED TO DECRYPT [regex]' };
            }

        } catch (e) {

            return { error: msg_prefix + 'FAILED TO DECRYPT [exception]' };

        }

        //
        // parse JSON
        //

        try {

            return {
                success: msg_prefix + 'Success',
                data:    JSON.parse( decrypted.replace( regexp, '' ) )
            };

        } catch (e) {

            console.log(e);
            return { error: msg_prefix + 'FAILED TO PARSE JSON' };

        }

    },


    delCookie: function()
    {

        if ( ! this.csrf_token )
        {
            return { error: 'CookieEncrypter not initialized' };
        }

        try {

            Cookies.remove( this.cookie_key, { path: this.cookie_path, sameSite: this.cookie_sameSite });
            return { success: 'CookieEncrypter.delCookie()' };

        } catch (e) { console.log(e); }

    },


    quoteRegex: function( str )
    {
        // https://stackoverflow.com/questions/494035/how-do-you-use-a-variable-in-a-regular-expression
        return str.replace( /([.?*+^$[\]\\(){}|-])/g, "\\$1" );
    },


}
