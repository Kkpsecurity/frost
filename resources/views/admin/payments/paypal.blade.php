@extends('adminlte::page')

@section('title', 'PayPal Configuration')

@section('content_header')
    <h1>
        <i class="fab fa-paypal"></i> PayPal Configuration
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

            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">PayPal API Credentials</h3>
                    </div>
                    <form action="{{ route('admin.payments.update-paypal') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                PayPal payments are coming soon. You can configure credentials now and they will be
                                activated when the PayPal integration is complete.
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
                                <label for="client_id">Client ID</label>
                                <input type="text" id="client_id" name="client_id" class="form-control"
                                    placeholder="{{ $hasClientId ? '••••••••••••' : 'PayPal Client ID' }}">
                                @if ($hasClientId)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Client ID configured — leave blank to keep
                                        existing.
                                    </small>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="client_secret">Client Secret</label>
                                <input type="password" id="client_secret" name="client_secret" class="form-control"
                                    placeholder="{{ $hasClientSecret ? '••••••••••••' : 'PayPal Client Secret' }}">
                                @if ($hasClientSecret)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Client Secret configured — leave blank to
                                        keep existing.
                                    </small>
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
