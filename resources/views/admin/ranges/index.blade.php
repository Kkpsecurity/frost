@extends('adminlte::page')

@section('title', 'Ranges Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Ranges Management</h1>
        <a href="{{ route('admin.ranges.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Range
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Ranges</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ranges.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search by Name</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Range name..."
                                   value="{{ $content['filters']['search'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>City</label>
                            <select name="city" class="form-control">
                                <option value="">All Cities</option>
                                @foreach($content['cities'] as $city)
                                    <option value="{{ $city }}" {{ ($content['filters']['city'] ?? '') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ ($content['filters']['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($content['filters']['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.ranges.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ranges Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                All Ranges
                <span class="badge badge-info">{{ $content['ranges']->total() }}</span>
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            @if($content['ranges']->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Price</th>
                            <th>Times</th>
                            <th>Appt Only</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($content['ranges'] as $range)
                            <tr>
                                <td>
                                    <strong>{{ $range->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $range->inst_name }}</small>
                                </td>
                                <td>{{ $range->city }}</td>
                                <td>
                                    <small>{{ $range->address }}</small>
                                </td>
                                <td>${{ number_format($range->price, 2) }}</td>
                                <td>
                                    <small>{{ $range->times }}</small>
                                </td>
                                <td>
                                    @if($range->appt_only)
                                        <span class="badge badge-warning">Yes</span>
                                    @else
                                        <span class="badge badge-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($range->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.ranges.show', $range) }}"
                                           class="btn btn-info"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ranges.edit', $range) }}"
                                           class="btn btn-primary"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.ranges.toggle-active', $range) }}"
                                              method="POST"
                                              style="display: inline;">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-{{ $range->is_active ? 'warning' : 'success' }}"
                                                    title="{{ $range->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $range->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-crosshairs fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No ranges found matching your criteria.</p>
                    <a href="{{ route('admin.ranges.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Range
                    </a>
                </div>
            @endif
        </div>
        @if($content['ranges']->hasPages())
            <div class="card-footer">
                {{ $content['ranges']->links() }}
            </div>
        @endif
    </div>
</div>
@stop
