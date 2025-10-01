@extends('adminlte::page')

@section('title', 'Media Manager')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Media Manager</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-photo-video text-muted" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="text-muted mb-3">Media Manager</h2>
                    <h4 class="text-warning mb-4">
                        <i class="fas fa-tools"></i> Coming Soon
                    </h4>

                    <p class="text-muted mb-4">
                        We're working hard to bring you a comprehensive media management system.
                        This feature will include:
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">File Upload</span>
                                    <span class="info-box-number">Drag & Drop</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-folder-open"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Organization</span>
                                    <span class="info-box-number">Smart Folders</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-search"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Search</span>
                                    <span class="info-box-number">Find Anything</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-share-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sharing</span>
                                    <span class="info-box-number">Easy Links</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Development Status</h5>
                            <p class="mb-2">
                                <strong>Current Storage:</strong> {{ ucfirst($currentDisk ?? 'local') }} disk<br>
                                <strong>Available Disks:</strong> {{ is_array($availableDisks ?? []) ? count($availableDisks) : 0 }} configured<br>
                                <strong>Categories:</strong> {{ is_array($categories ?? []) ? count($categories) : 0 }} media types supported
                            </p>
                            <small class="text-muted">
                                The backend infrastructure is ready - we're now building the user interface!
                            </small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Dashboard
                        </a>
                        <button class="btn btn-outline-secondary" onclick="alert('We will notify you when the Media Manager is ready!')">
                            <i class="fas fa-bell"></i> Notify Me
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-hdd"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Storage Disk</span>
                                    <span class="info-box-number">{{ ucfirst($currentDisk ?? 'Not Set') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-database"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Available Disks</span>
                                    <span class="info-box-number">{{ is_array($availableDisks ?? []) ? count($availableDisks) : 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-tags"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Categories</span>
                                    <span class="info-box-number">{{ is_array($categories ?? []) ? count($categories) : 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-cogs"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">In Development</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($stats) && is_array($stats))
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>Storage Statistics</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        @foreach($stats as $key => $value)
                                            <tr>
                                                <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong></td>
                                                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            transition: transform 0.2s;
        }
        .info-box:hover {
            transform: translateY(-2px);
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Media Manager - Coming Soon page loaded');

        // Add some interactivity
        $('.info-box').hover(
            function() {
                $(this).addClass('shadow-lg');
            },
            function() {
                $(this).removeClass('shadow-lg');
            }
        );
    </script>
@stop
