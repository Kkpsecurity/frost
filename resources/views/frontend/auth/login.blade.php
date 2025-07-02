@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('content')
    @include('frontend.partials.breadcrumbs')
    
    <div class="login-area">
        <div class="login-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="border-1 shadow-lg">
                        <div class="login-form-container p-5">
                            <h3 class="login-title mb-4">LOGIN</h3>
                            <div class="row">
                                <div id="message-console"></div>
                                @include('frontend.partials.messages')
                                @include('frontend.forms.login-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop



@section('scripts')
    <script>
        $(document).ready(function() {
            $('#login-form').submit(function(e) {
                    e.preventDefault(); // prevent the form from being submitted

                    // get the values of the input fields
                    var email = $('#email').val();
                    var password = $('#password').val();

                    // check if the remember me checkbox is checked
                    var remember_me = $('input[name="remember_me"]').is(':checked');

                    // get the CSRF token
                    var csrf_token = $('input[name="_token"]').val(); // get the CSRF token

                    // validate the input fields
                    if (!email) {
                        // email is empty
                        $('#message-console').append(
                            '<p class="alert alert-danger">Please enter your email address</p>');
                        setTimeout(function() {
                            $('#message-console p').fadeOut();
                        }, 4000);
                        return;
                    } else if (!password) {
                        // password is empty
                        $('#message-console').append(
                            '<p class="alert alert-danger">Please enter your password</p>');
                        setTimeout(function() {
                            $('#message-console p').fadeOut();
                        }, 4000);
                        return;
                    }

                    // send an AJAX request to the server to verify the credentials
                    $.ajax({
                            type: 'POST',
                            url: '/login',
                            data: {
                                _token: csrf_token, // pass the CSRF token to the server
                                email: email,
                                password: password,
                                remember_me: remember_me
                            }),
                        success: function(response) {
                            // handle the response from the server
                            if (response.success) {
                                // login was successful
                                // show a success message and hide it after 4 seconds
                                $('#message-console').append(
                                    '<p class="alert alert-success">Login was successful</p>');
                                setTimeout(function() {
                                    $('#message-console p').fadeOut();
                                }, 4000);

                                // redirect the user to the home page
                                window.location.href = response.redirect_url;
                            } else {
                                // login was unsuccessful
                                // show an error message and hide it after 4 seconds
                                $('#message-console').append(
                                    '<p class="alert alert-danger">Login was unsuccessful</p>');
                                setTimeout(function() {
                                    $('#message-console p').fadeOut();
                                }, 4000);
                            }
                        }
                    }
                );
            });
        });
    </script>
@endsection
