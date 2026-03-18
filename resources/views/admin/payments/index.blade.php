@extends('adminlte::page')

@section('title', 'Payment Settings')

@section('content_header')
    <h1>
        <i class="fas fa-credit-card"></i> Payment Settings
    </h1>
@stop

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">

            {{-- Stripe Card --}}
            <div class="col-md-6 mb-4">
                <div class="card card-outline {{ $stripeConfigured ? 'card-success' : 'card-secondary' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-stripe mr-2"></i> Stripe
                        </h3>
                        <div class="card-tools">
                            <span class="badge {{ $stripeConfigured ? 'badge-success' : 'badge-secondary' }}">
                                {{ $stripeConfigured ? 'Configured' : 'Not Configured' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Accept credit and debit card payments via Stripe.</p>
                        <a href="{{ route('admin.payments.stripe') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog mr-1"></i> Configure Stripe
                        </a>
                    </div>
                </div>
            </div>

            {{-- PayPal Card --}}
            <div class="col-md-6 mb-4">
                <div class="card card-outline {{ $paypalConfigured ? 'card-success' : 'card-secondary' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-paypal mr-2"></i> PayPal
                        </h3>
                        <div class="card-tools">
                            <span class="badge {{ $paypalConfigured ? 'badge-success' : 'badge-secondary' }}">
                                {{ $paypalConfigured ? 'Configured' : 'Not Configured' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Allow students to pay using their PayPal account.</p>
                        <a href="{{ route('admin.payments.paypal') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog mr-1"></i> Configure PayPal
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
