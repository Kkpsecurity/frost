/*  begin PayFlowForm.js  */
PayFlowProForm = {

    card_input_ids: [ 'card_num', 'card_exp', 'card_csc' ],

    cleave_num:     null,
    cleave_exp:     null,
    cleave_csc:     null,


    init: function( debug )
    {

        this.debug = debug;

        this.setFormSubmit();
        this.formatFormInputs();
        this.activateValidation();

        this.Debug( 'ValidateForm()' );
        this.ValidateForm();


        if ( this.debug )
        {
            this.cleave_num.setRawValue( '4032036621825007' );
            this.cleave_exp.setRawValue( '1226' );
            this.cleave_csc.setRawValue( '284' );
            this.ValidateForm();
        }

    },


    /**
     *
     * set form submit
     *
     */

    setFormSubmit: function()
    {

        this.Debug( 'setSubmitAction()' );

        $( 'form#paymentform' ).on( 'submit', function (e) {

            e.preventDefault();

            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            $( '#paymentModal' ).modal( 'show' );

            //
            // copy clean (raw) inputs to hidden fields
            //

            $( 'input[name="ACCT"]'    ).val( PayFlowProForm.cleave_num.getRawValue() );
            $( 'input[name="EXPDATE"]' ).val( PayFlowProForm.cleave_exp.getRawValue() );
            $( 'input[name="CSC"]'     ).val( PayFlowProForm.cleave_csc.getRawValue() );

            //
            // get token data, then submit
            //

            $.post( token_route, function( res ) {

                PayFlowProForm.Debug( 'SECURETOKENID ' + res.SECURETOKENID );
                PayFlowProForm.Debug( 'SECURETOKEN   ' + res.SECURETOKEN   );

                $( 'input[name="SECURETOKENID"]' ).val( res.SECURETOKENID );
                $( 'input[name="SECURETOKEN"]'   ).val( res.SECURETOKEN   );

                e.target.submit();

            }, 'json' )
            .fail( function( res ) { alert( "Server Fail:\n" + res.responseText ); });

        });

    },


    /**
     *
     * form validation
     *
     */

    activateValidation: function()
    {

        this.Debug( 'activateValidation()' );

        $.each( PayFlowProForm.card_input_ids, function( idx, key ) {
            $( '#' + key )
                .keypress(function() { PayFlowProForm.ValidateForm(); })
                .keyup(function()    { PayFlowProForm.ValidateForm(); })
                .change(function()   { PayFlowProForm.ValidateForm(); });
        });

    },

    ValidateForm: function()
    {

        let card_num = PayFlowProForm.cleave_num.getRawValue();

        if ( ! card_num.length )
        {
            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            return;
        }

        let card_validator = CardValidator.number( card_num );

        //
        // card number
        //

        if ( ! card_validator.isPotentiallyValid )
        {
            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            return;
        }

        if ( ! card_validator.card.lengths.includes( card_num.length ) )
        {
            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            return;
        }

        //
        // expiration
        //

        if ( PayFlowProForm.cleave_exp.getRawValue().length != 4 )
        {
            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            return;
        }

        //
        // csc
        //

        if ( PayFlowProForm.cleave_csc.getRawValue().length != card_validator.card.code.size )
        {
            $( '#paymentSubmitBtn' ).attr( 'disabled', true );
            return;
        }

        //
        // success
        //

        $( '#paymentSubmitBtn' ).attr( 'disabled', false );

    },


    /**
     *
     * apply input field formatters
     *
     */

    formatFormInputs: function()
    {

        this.Debug( 'formatFormInputs()' );

        this.cleave_num = new Cleave( '#card_num', {
            creditCard: true,
        });

        this.cleave_exp = new Cleave( '#card_exp', {
            date: true,
            datePattern: [ 'm', 'y' ]
        });

        this.cleave_csc = new Cleave( '#card_csc', {
            blocks: [4],
            numericOnly: true,
        });

    },


    /**
     *
     * debugging
     *
     */

    debug: false,
    Debug: function( msg )
    {
        if ( this.debug ) console.log( '(PayFlowProForm) ' + msg );
    },


}
/*  end PayFlowForm.js  */
