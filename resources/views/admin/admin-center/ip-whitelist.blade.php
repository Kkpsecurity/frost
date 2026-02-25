@extends('adminlte::page')

@section('title', 'IP Whitelist')

@section('content_header')
    <h1><i class="fas fa-shield-alt"></i> IP Whitelist</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Not Yet Implemented</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        IP whitelisting requires a dedicated enforcement table and middleware.
                        This feature is planned for a future release.
                    </p>
                    <p class="mt-2 mb-0">
                        Your server-level firewall (e.g. Nginx, UFW) is the recommended approach
                        for IP-based access control in the interim.
                    </p>
                </div>
            </div>
        </div>
    </div>

@stop
