@extends('adminlte::page')

@section('title', 'PayPal Configuration')

@section('content_header')
    <h1>
        <i class="fab fa-paypal"></i> PayPal Configuration
    </h1>
@stop

@section('content')
    <div class="container-fluid paypal-config-page">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">

            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">PayPal API Credentials</h3>
                    </div>
                    <form action="{{ route('admin.payments.update-paypal') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                If these fields are left blank, PayPal will use the environment configuration.
                                Fill them in to override the env values.
                            </div>

                            <div class="form-group">
                                <label>Mode</label>
                                <div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input class="custom-control-input" type="radio" id="mode_sandbox" name="mode"
                                            value="sandbox" {{ ($mode ?? 'sandbox') === 'sandbox' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_sandbox">Sandbox</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input class="custom-control-input" type="radio" id="mode_live" name="mode"
                                            value="live" {{ ($mode ?? 'sandbox') === 'live' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_live">Live</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="paypal_client_id">Client ID</label>
                                <input type="text" id="paypal_client_id" name="paypal_client_id" class="form-control"
                                    autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false"
                                    inputmode="text" data-lpignore="true" data-1p-ignore="true" readonly
                                    onfocus="this.removeAttribute('readonly');"
                                    value="{{ old('paypal_client_id', $settingsClientId ?? '') }}"
                                    placeholder="{{ $hasSettingsClientId ?? false ? '••••••••••••' : 'PayPal Client ID' }}">
                                @if ($hasSettingsClientId ?? false)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Using settings key
                                        <code>payments.paypal.client_id</code>.
                                    </small>
                                @elseif($hasEnvClientId ?? false)
                                    <small class="form-text text-info">
                                        <i class="fas fa-info-circle mr-1"></i> No settings override — using environment
                                        configuration.
                                    </small>
                                @else
                                    <small class="form-text text-muted">Settings key:
                                        <code>payments.paypal.client_id</code></small>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="paypal_client_secret">Client Secret</label>
                                <input type="password" id="paypal_client_secret" name="paypal_client_secret"
                                    class="form-control" autocomplete="new-password" autocorrect="off" autocapitalize="none"
                                    spellcheck="false" data-lpignore="true" data-1p-ignore="true" readonly
                                    onfocus="this.removeAttribute('readonly');"
                                    placeholder="{{ $hasSettingsClientSecret ?? false ? '••••••••••••' : 'PayPal Client Secret' }}">
                                @if ($hasSettingsClientSecret ?? false)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Using settings key
                                        <code>payments.paypal.client_secret</code>.
                                    </small>
                                @elseif($hasEnvClientSecret ?? false)
                                    <small class="form-text text-info">
                                        <i class="fas fa-info-circle mr-1"></i> No settings override — using environment
                                        configuration.
                                    </small>
                                @else
                                    <small class="form-text text-muted">Settings key:
                                        <code>payments.paypal.client_secret</code></small>
                                @endif
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save PayPal Settings
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Instructions</h3>
                    </div>
                    <div class="card-body">
                        <p>Get your API credentials from the
                            <a href="https://developer.paypal.com/developer/applications" target="_blank"
                                rel="noopener noreferrer">
                                PayPal Developer Portal
                            </a>.
                        </p>
                        <ul>
                            <li>Create a <strong>REST API app</strong> to get credentials</li>
                            <li>Use <strong>Sandbox</strong> for development</li>
                            <li>Switch to <strong>Live</strong> for production only</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('css')
    <style>
        /* Fix low-contrast Chrome autofill styling on dark AdminLTE pages */
        .paypal-config-page input.form-control:-webkit-autofill,
        .paypal-config-page input.form-control:-webkit-autofill:hover,
        .paypal-config-page input.form-control:-webkit-autofill:focus,
        .paypal-config-page input.form-control:-webkit-autofill:active {
            -webkit-text-fill-color: #fff;
            caret-color: #fff;
            box-shadow: 0 0 0 1000px #343a40 inset;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
@stop
