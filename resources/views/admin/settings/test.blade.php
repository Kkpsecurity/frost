@extends('adminlte::page')

@section('title', 'Settings Test')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Settings System Test</h1>
        <div>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Results</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Refresh Tests
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($tests as $testName => $result)
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-{{ $result ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $result ? 'check' : 'times' }}"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ ucwords(str_replace('_', ' ', $testName)) }}</span>
                                        <span class="info-box-number">
                                            {{ $result ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Test Summary</h5>
                        <p>
                            Passed: <strong>{{ count(array_filter($tests)) }}</strong> |
                            Failed: <strong>{{ count($tests) - count(array_filter($tests)) }}</strong> |
                            Total: <strong>{{ count($tests) }}</strong>
                        </p>
                        @if(count($tests) === count(array_filter($tests)))
                            <p class="text-success mb-0">
                                <i class="fas fa-check-circle"></i> All tests passed! The settings system is working correctly.
                            </p>
                        @else
                            <p class="text-danger mb-0">
                                <i class="fas fa-exclamation-triangle"></i> Some tests failed. Please check your settings configuration.
                            </p>
                        @endif
                    </div>

                    <!-- Test Details -->
                    <div class="mt-4">
                        <h5>Test Details</h5>
                        <div class="accordion" id="testAccordion">
                            <div class="card">
                                <div class="card-header" id="basicTest">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#basicTestContent">
                                            Basic Set Test {{ $tests['basic_set'] ? '✓' : '✗' }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="basicTestContent" class="collapse" data-parent="#testAccordion">
                                    <div class="card-body">
                                        <p>Tests basic setting storage and retrieval without prefix.</p>
                                        <code>Setting::set('test_setting', 'test_value')</code><br>
                                        <code>Setting::get('test_setting') === 'test_value'</code>
                                        <p class="mt-2">
                                            <strong>Result:</strong>
                                            <span class="badge badge-{{ $tests['basic_set'] ? 'success' : 'danger' }}">
                                                {{ $tests['basic_set'] ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="helperTest">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#helperTestContent">
                                            Helper Set Test {{ $tests['helper_set'] ? '✓' : '✗' }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="helperTestContent" class="collapse" data-parent="#testAccordion">
                                    <div class="card-body">
                                        <p>Tests SettingHelper with prefix functionality.</p>
                                        <code>$settingHelper = new SettingHelper('test')</code><br>
                                        <code>$settingHelper->set('helper_setting', 'helper_value')</code><br>
                                        <code>$settingHelper->get('helper_setting') === 'helper_value'</code>
                                        <p class="mt-2">
                                            <strong>Result:</strong>
                                            <span class="badge badge-{{ $tests['helper_set'] ? 'success' : 'danger' }}">
                                                {{ $tests['helper_set'] ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="prefixTest">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#prefixTestContent">
                                            Prefix Working Test {{ $tests['prefix_working'] ? '✓' : '✗' }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="prefixTestContent" class="collapse" data-parent="#testAccordion">
                                    <div class="card-body">
                                        <p>Verifies that prefix is properly applied to setting keys.</p>
                                        <code>Setting::get('test.helper_setting') === 'helper_value'</code>
                                        <p class="mt-2">
                                            <strong>Result:</strong>
                                            <span class="badge badge-{{ $tests['prefix_working'] ? 'success' : 'danger' }}">
                                                {{ $tests['prefix_working'] ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="countTest">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#countTestContent">
                                            All Settings Count Test {{ $tests['all_settings_count'] ? '✓' : '✗' }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="countTestContent" class="collapse" data-parent="#testAccordion">
                                    <div class="card-body">
                                        <p>Checks if settings can be retrieved as a collection.</p>
                                        <code>count(Setting::all()) > 0</code>
                                        <p class="mt-2">
                                            <strong>Settings Count:</strong> {{ count($allSettings ?? []) }}<br>
                                            <strong>Result:</strong>
                                            <span class="badge badge-{{ $tests['all_settings_count'] ? 'success' : 'danger' }}">
                                                {{ $tests['all_settings_count'] ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Information</h3>
                </div>
                <div class="card-body">
                    <h6>Settings Package</h6>
                    <p class="text-muted">
                        <code>akaunting/laravel-setting</code>
                    </p>

                    <h6>Database Table</h6>
                    <p class="text-muted">
                        <code>settings</code>
                    </p>

                    <h6>Total Settings</h6>
                    <p class="text-muted">
                        <strong>{{ count($allSettings ?? []) }}</strong> settings found
                    </p>

                    <h6>Cache Status</h6>
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> Settings are cached by the package
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list"></i> View All Settings
                        </a>
                        <a href="{{ route('admin.settings.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Create New Setting
                        </a>
                        <button class="btn btn-info btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Run Tests Again
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="clearCache()">
                            <i class="fas fa-trash"></i> Clear Settings Cache
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Troubleshooting</h3>
                </div>
                <div class="card-body">
                    <h6>Common Issues</h6>
                    <ul class="text-muted" style="font-size: 12px;">
                        <li>Missing settings table migration</li>
                        <li>Database connection issues</li>
                        <li>Cache not clearing properly</li>
                        <li>Incorrect package configuration</li>
                    </ul>

                    <h6>Debug Commands</h6>
                    <ul class="text-muted" style="font-size: 12px;">
                        <li><code>php artisan migrate</code></li>
                        <li><code>php artisan cache:clear</code></li>
                        <li><code>php artisan config:clear</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function clearCache() {
            if (confirm('Are you sure you want to clear the settings cache?')) {
                // This would make an AJAX request to clear cache
                alert('Cache clearing would be implemented here. Add a route/method to clear cache.');
            }
        }

        // Auto-expand failed tests
        document.addEventListener('DOMContentLoaded', function() {
            @if(count($tests) !== count(array_filter($tests)))
                // Expand first failed test
                @foreach($tests as $testName => $result)
                    @if(!$result)
                        $('#{{ $testName }}Content').collapse('show');
                        @break
                    @endif
                @endforeach
            @endif
        });
    </script>
@stop
