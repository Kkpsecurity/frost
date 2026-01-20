@extends('adminlte::page')

@section('title', 'Instructor Management')

@section('content_header')
    <h1>Instructor Management</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_instructors'] }}</h3>
                        <p>Total Instructors</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['active_instructors'] }}</h3>
                        <p>Active Instructors</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['total_classes_taught'] }}</h3>
                        <p>Total Classes Taught</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructors Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Instructors</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Classes Taught</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->id }}</td>
                                <td>
                                    <strong>{{ $instructor->fullname() }}</strong>
                                </td>
                                <td>{{ $instructor->email }}</td>
                                <td>
                                    @if($instructor->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $instructor->instUnits->count() }}</span>
                                </td>
                                <td>{{ $instructor->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.admin-center.instructors.show', $instructor->id) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.admin-center.instructors.edit', $instructor->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.admin-center.instructors.toggle-status', $instructor->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @if($instructor->is_active)
                                                <button type="submit" class="btn btn-sm btn-warning" title="Deactivate" onclick="return confirm('Deactivate this instructor?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-success" title="Activate" onclick="return confirm('Activate this instructor?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No instructors found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($instructors->hasPages())
            <div class="card-footer clearfix">
                {{ $instructors->links() }}
            </div>
            @endif
        </div>
    </div>
@stop
