@extends('layouts.admin')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('admin.partials.admin-messages')
                    <div id="message-console"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h3 class="card-title"><i class="fa fa-document"></i> {{ __('Server Logs') }}</h3>
                                </div>
                                <div class="col-lg-6">
                                    <div class="float-right">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Timestamp</th>
                                                <th>Environment</th>
                                                <th>Level</th>
                                                <th>Message</th>
                                                <th>Key</th>
                                                <th>Value</th>
                                                <th>Exception</th>
                                                <th>Stacktrace</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($logs as $index => $log)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>

                                                    @if (isset($log['raw']))
                                                        <td colspan="8">{{ $log['raw'] }}</td>
                                                    @else
                                                        <td>{{ $log['timestamp'] }}</td>
                                                        <td>{{ $log['environment'] }}</td>
                                                        <td>{{ $log['log_level'] }}</td>
                                                        <td>{{ $log['message'] }}</td>
                                                        <td>{{ $log['key'] }}</td>
                                                        <td>{{ $log['value'] }}</td>
                                                        <td>{{ $log['exception'] ?? '-' }}</td>
                                                        <td>{{ $log['stacktrace'] ?? '-' }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {!! $logs->links('pagination::bootstrap-4'); !!}
                                }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
