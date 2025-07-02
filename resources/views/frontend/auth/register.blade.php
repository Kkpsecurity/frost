@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@php $registrationEnabled = true; @endphp
@section('content')
    @include('frontend.partials.breadcrumbs')

    <div class="login-area">
        <div class="login-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="border-1 shadow-lg">
                        <div class="login-form-container p-5">
                            <h3 class="login-title mb-4">CREATE ACCOUNT</h3>
                            @if ($registrationEnabled)
                                <p class="text-white-50 mb-4">Please fill out the form below to create a new account.</p>
                                <div id="message-console"></div>
                                @include('frontend.forms.registration-form')
                            @else
                                <div class="alert alert-danger">Registration is currently disabled.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')

    <script>
        $(document).ready(function() {
            $('form#register-form').submit(function(event) {
                event.preventDefault(); // prevent the form from being submitted

                // get the form elements
                var form = $(this);
                var name = form.find('#name').val();
                var email = form.find('#email').val();
                var password = form.find('#password').val();
                var passwordConfirm = form.find('#password_confirmation').val();
                var csrf_token = form.find('input[name="_token"]').val(); // get the CSRF token

                // validate the input fields
                if (!name) {
                    // name is empty
                    alert('Please enter your name.');
                    form.find('#name').focus();
                    return;
                } else if (!email) {
                    // email is empty
                    alert('Please enter your email address.');
                    form.find('#email').focus();
                    return;
                } else if (!password) {
                    // password is empty
                    alert('Please enter a password.');
                    form.find('#password').focus();
                    return;
                } else if (!passwordConfirm) {
                    // password confirmation is empty
                    alert('Please confirm your password.');
                    form.find('#password_confirmation').focus();
                    return;
                } else if (password !== passwordConfirm) {
                    // password and password confirmation do not match
                    alert('The password and password confirmation do not match.');
                    form.find('#password_confirmation').focus();
                    return;
                }

                // send an AJAX request to the server to register the user
                $.ajax({
                    type: 'POST',
                    url: '/register',
                    data: {
                        _token: csrf_token, // pass the CSRF token to the server
                        name: name,
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirm
                    },
                    success: function(response) {
                        // handle the response from the server
                        if (response.success) {
                            // registration was successful
                            // show a success message and redirect the user to the login page after 4 seconds
                            alert(
                                'Registration was successful. Redirecting to the login page...'
                                );
                            setTimeout(function() {
                                window.location.href = '/login';
                            }, 4000);
                        } else {
                            // registration was unsuccessful
                            // show an error message
                            alert('Registration was unsuccessful. Please try again.');
                        }
                    }
                });
            });
        });
    </script>


@endsection
