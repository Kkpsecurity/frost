@extends('adminlte::page')

@section('title', 'Settings Test')

@section('content_header')
    <x-admin.admin-header />
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settings Functionality Test Results</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <h4>Test Results</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Test</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic Setting</td>
                                    <td>
                                        @if($tests['basic_set'])
                                            <span class="badge badge-success"><i class="fas fa-check"></i> PASS</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> FAIL</span>
                                        @endif
                                    </td>
                                    <td>Can set and retrieve basic settings without prefix</td>
                                </tr>
                                <tr>
                                    <td>Helper with Prefix</td>
                                    <td>
                                        @if($tests['helper_set'])
                                            <span class="badge badge-success"><i class="fas fa-check"></i> PASS</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> FAIL</span>
                                        @endif
                                    </td>
                                    <td>SettingHelper can set and retrieve settings with prefix</td>
                                </tr>
                                <tr>
                                    <td>Prefix Functionality</td>
                                    <td>
                                        @if($tests['prefix_working'])
                                            <span class="badge badge-success"><i class="fas fa-check"></i> PASS</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> FAIL</span>
                                        @endif
                                    </td>
                                    <td>Prefix is correctly applied to setting keys</td>
                                </tr>
                                <tr>
                                    <td>Database Connection</td>
                                    <td>
                                        @if($tests['all_settings_count'])
                                            <span class="badge badge-success"><i class="fas fa-check"></i> PASS</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> FAIL</span>
                                        @endif
                                    </td>
                                    <td>Can retrieve settings from database</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h4>Current Settings in Database</h4>
                    @if(count($allSettings) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allSettings as $key => $value)
                                        <tr>
                                            <td><code>{{ $key }}</code></td>
                                            <td>
                                                @if(is_array($value) || is_object($value))
                                                    <pre class="small">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ gettype($value) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No settings found in database.
                        </div>
                    @endif

                    <hr>

                    <h4>Usage Examples</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>SettingHelper Usage</h5>
                            <pre class="bg-light p-3"><code>// Set prefix
SettingHelper::setPrefix('adminlte');

// Set settings
SettingHelper::set('title', 'My Admin Panel');
SettingHelper::set('layout.dark_mode', true);

// Get settings
$title = SettingHelper::get('title', 'Default');
$darkMode = SettingHelper::get('layout.dark_mode', false);

// Get all with prefix
$all = SettingHelper::all();</code></pre>
                        </div>
                        <div class="col-md-6">
                            <h5>Direct Akaunting Usage</h5>
                            <pre class="bg-light p-3"><code>// Direct usage
Setting::set('key', 'value');
$value = Setting::get('key', 'default');

// Stored as actual key names:
// adminlte.title
// adminlte.layout.dark_mode</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    @vite('resources/css/admin.css')
<style>
pre {
    font-size: 0.85em;
}
.table-responsive {
    font-size: 0.9em;
}
</style>
@stop
