@extends('layouts.app')

@section('page-title')
    {{ $content['title'] }}
@stop

@section('page-keywords')
    {{ $content['keywords'] }}
@stop

@section('page-description')
    {{ $content['description'] }}
@stop

@section('styles')
    <style>
        .payment-container {
            width: 100%;
            background-color: #f5f5f5;
            /* Light gray background color */
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 20px;
        }

        .payment-container .form-label {
            font-weight: 800;
        }

        .payment-container .form-control {
            border-radius: 5px;
            background-color: #f0f8ff;
            /* Light blue background color */
        }

        #total-payment {
            background-color: #f5f5f5;
            /* Light gray background color */
            margin: 0 auto;
            max-width: 600px;
        }

        .payment-container h3 {
            text-align: center;
            font-size: 2em;
        }

        #couponForm,
        #paymentform {
            margin: 0 auto;
            padding: 20px;
            background: #f0f8ff;
            /* Light blue background color */
            max-width: 600px;
        }

        #couponForm .form-label {
            font-weight: 800;
        }

        #couponForm .form-control {
            border-radius: 5px;
            background-color: #f5f5f5;
            /* Light gray background color */
        }

        .payment-container .btn-s2wp-gray {
            background-color: #ddd;
            border-color: #ddd;
            color: #333;
        }

        .payment-container .btn-s2wp-gray:hover,
        .payment-container .btn-s2wp-gray:active,
        .payment-container .btn-s2wp-gray:focus {
            background-color: #ccc;
            border-color: #ccc;
            color: #333;
        }

        .payment-container .btn-s2wp-gray:disabled {
            background-color: #ddd;
            border-color: #ddd;
            color: #333;
        }

        .form-group.invalid-alt {
            margin-bottom: 1.5rem;
        }

        .form-group.invalid-alt .form-label {
            padding-left: 0.5rem;
        }

        .form-group.invalid-alt .input-group {
            margin-bottom: 1rem;
        }

        .form-group.invalid-alt .invalid-feedback {
            padding-left: 0.5rem;
        }

        .modal-body .spinner-border {
            margin-top: 0.25rem;
            margin-right: 0.75rem;
        }

        .modal-body .fs-3 {
            font-size: 1.5rem;
            color: #333;
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .payment-container {
                padding: 10px;
            }

            #total-payment,
            #couponForm,
            #paymentform {
                width: 100%;
                max-width: none;
            }
        }
    </style>

@stop

@section('content')
    @include('frontend.partials.breadcrumbs')

    <div class="content-maroon py-3">
        @include('frontend.partials.messages')
        <div class="container payment-container mb-3">
            <div class="row mb-3">
                <h3 class="text-center">Course Payment</h3>
            </div>

            <div class="row mb-2">
                <div class="form-group">
                    <ul class="list-group" id="total-payment">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-2 me-3 text-nowrap">Course</div>
                                <div class="col ps-4 text-end bold">{{ $Course->title_long }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-2 me-3 text-nowrap">Course Price</div>
                                <div class="col ps-4 text-end bold">${{ $Order->course_price }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-2 me-3 text-nowrap">Total Price</div>
                                <div class="col ps-4 text-end bold">${{ $PayFlowPro->total_price }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @if (!$Order->discount_code_id)
                        <form method="POST" action="{{ route('order.applydiscountcode', $Order) }}" accept-charset="UTF-8"
                            id="couponForm">
                            @csrf
                            <div class="form-group invalid-alt mb-3">
                                <label for="discount_code" class="form-label ps-2">Discount Code</label>
                                <div class="input-group mb-4">
                                    <input class="form-control @if ($errors->has('discount_code')) is-invalid @endif"
                                        type="text" name="discount_code" id="discount_code"
                                        value="{{ old('discount_code') }}" />
                                    <button class="btn btn-s2wp btn-s2wp-gray" type="submit">Apply</button>
                                    @error('discount_code')
                                        <div class="invalid-feedback ps-2" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <form method="POST" action="{{ $payflow_route }}" accept-charset="UTF-8" id="paymentform">
                        <input type="hidden" name="CANCELURL" value="{{ $return_route }}" />
                        <input type="hidden" name="ERRORURL" value="{{ $return_route }}" />
                        <input type="hidden" name="RETURNURL" value="{{ $return_route }}" />
                        <input type="hidden" name="SECURETOKENID" value="" />
                        <input type="hidden" name="SECURETOKEN" value="" />
                        <input type="hidden" name="CSCREQUIRED" value="TRUE" />
                        <input type="hidden" name="TENDER" value="C" />
                        <input type="hidden" name="ACCT" value="" />
                        <input type="hidden" name="EXPDATE" value="" />
                        <input type="hidden" name="CSC" value="" />

                        <div class="mb-2">
                            <label for="card_num" class="form-label ps-2">Card Number</label>
                            {{-- @if ($PayFlowPro->pp_is_sandbox)
                                <span class="badge bg-warning text-dark mt-1 float-end">Sandbox</span>
                            @endif --}}
                            <input id="card_num" type="numeric" class="form-control form-control-lg" required />
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="card_exp" class="form-label ps-2">Expiration
                                        <span class="d-none d-sm-inline">Date</span>
                                    </label>
                                    <input id="card_exp" type="numeric" class="form-control form-control-lg" required />
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="card_csc" class="form-label ps-2" data-bs-toggle="tooltip"
                                        title="Three or four digit CV code on the back of your card">
                                        <i class="fa fa-question-circle d-none d-sm-inline ms-0 me-1"></i> Security Code
                                    </label>
                                    <input id="card_csc" type="numeric" class="form-control form-control-lg" required />
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="paymentSubmitBtn" class="btn btn-lg btn-s2wp btn-s2wp-gray"
                                disabled>
                                <i class="fa fa-plus me-2"></i> Confirm Payment
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex align-content-center justify-content-center">
                        <div class="spinner-border text-primary mt-1 me-3" role="status"></div>
                        <div class="fs-3 text-dark text-nowrap">Submitting Payment</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="/sat/PayFlowProForm.js"></script>
    <script>
        var token_route = '{{ $token_route }}';
        window.addEventListener('load', function() {
            PayFlowProForm.init(
                @if (Auth::user()->IsSysAdmin())
                    true
                @endif );
        });
    </script>
@stop