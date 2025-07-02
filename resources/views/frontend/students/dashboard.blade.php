@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('styles')
    <style>
        /* Default styling for larger screens */

        /* You can add any default styles here, e.g., */
        .dashboard {
            padding: 20px 0;
        }

        /* Mobile styling */
        @media (max-width: 767.98px) {

            .dashboard {
                padding: 10px 0;
                /* Adjust the dashboard padding */
            }

            .alert {
                flex-direction: column;
                /* stack alert text and button vertically */
                align-items: start;
            }

            .alert .btn {
                margin-top: 10px;
                /* some spacing between the alert text and button */
            }

            /* adjust font sizes */
            h3 {
                font-size: 1.5rem;
                /* or any other size that fits well */
            }

            /* Adjust margins for top elements to ensure they don't stick to the breadcrumbs or other elements above them */
            .mt-3 {
                margin-top: 1rem !important;
            }

            .mt-4 {
                margin-top: 1.5rem !important;
            }

        }

        /* Default styling for larger screens */
        .table {
            display: table;
            /* or whatever your default is */
        }

        .list-group-table {
            display: none;
            /* Hide list group by default */
        }

        /* Mobile styling */
        @media (max-width: 767.98px) {
            .table {
                display: none;
                /* Hide table on small screens */
            }

            .list-group-table {
                display: block;
                /* Show list group on small screens */
            }

            /* Rest of your mobile-specific styles... */
        }
    </style>
@stop

@section('content')
    @include('frontend.partials.breadcrumbs')
    <section class="dashboard bg-light" style="min-height: 600px;">
        <div class="container">
            <div class="row">

                @if (Auth()->User()->email_verified_at === null)
                    <div class="col-lg-12">
                        @if (session('verificationEmailSent'))
                            <div class="alert alert-warning shadow mt-3 d-flex justify-content-between" role="alert">
                                <i class="fa fa-info-circle"></i> {{ __('Verification email has already been sent') }},
                            </div>
                        @else
                            <div class="alert alert-danger shadow mt-3 d-flex justify-content-between" role="alert">
                                <span><i class="fa fa-exclamation-triangle"></i>
                                    {{ __('Your have not verified your email') }}</span>
                                <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-rounded btn-sm btn-outline-info ml-2"
                                        style="color: #0f0f10 !important;">{{ __('Click here to resend.') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif


                <div class="col-lg-12 mt-4 table">
                    @include('frontend.students.partials.auth_courses_table')
                </div>

                <div class="col-lg-12 mt-4 list-group-table">
                    @include('frontend.students.partials.auth_courses_moblie_list')
                </div>
            </div>
        </div>
    </section>
@stop
