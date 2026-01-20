@extends('adminlte::page')

@section('title', 'Range Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Range Details</h1>
        <div>
            <a href="{{ route('admin.ranges.edit', $content['range']) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.ranges.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
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

    <div class="row">
        <!-- Left Column - Range Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Range Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8"><strong>{{ $content['range']->name }}</strong></dd>

                        <dt class="col-sm-4">City:</dt>
                        <dd class="col-sm-8">{{ $content['range']->city }}</dd>

                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $content['range']->address }}</dd>

                        <dt class="col-sm-4">Price:</dt>
                        <dd class="col-sm-8">
                            <strong class="text-success">${{ number_format($content['range']->price, 2) }}</strong>
                        </dd>

                        <dt class="col-sm-4">Available Times:</dt>
                        <dd class="col-sm-8">{{ $content['range']->times }}</dd>

                        <dt class="col-sm-4">Appointment Only:</dt>
                        <dd class="col-sm-8">
                            @if($content['range']->appt_only)
                                <span class="badge badge-warning">
                                    <i class="fas fa-calendar-check"></i> Yes
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="fas fa-door-open"></i> Walk-ins Welcome
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($content['range']->is_active)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Active
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times"></i> Inactive
                                </span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Instructor/Contact Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contact Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Instructor Name:</dt>
                        <dd class="col-sm-8">{{ $content['range']->inst_name }}</dd>

                        @if($content['range']->inst_email)
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <a href="mailto:{{ $content['range']->inst_email }}">
                                    {{ $content['range']->inst_email }}
                                </a>
                            </dd>
                        @endif

                        @if($content['range']->inst_phone)
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">
                                <a href="tel:{{ $content['range']->inst_phone }}">
                                    {{ $content['range']->inst_phone }}
                                </a>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Column - Additional Details -->
        <div class="col-md-6">
            @if($content['range']->range_html)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Description</h3>
                    </div>
                    <div class="card-body">
                        {!! $content['range']->range_html !!}
                    </div>
                </div>
            @endif

            <!-- Range Dates -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Associated Range Dates
                        <span class="badge badge-info">{{ $content['range']->RangeDates->count() }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($content['range']->RangeDates->count() > 0)
                        <div class="list-group">
                            @foreach($content['range']->RangeDates as $rangeDate)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-calendar"></i>
                                            {{ $rangeDate->date ? $rangeDate->date->format('M d, Y') : 'TBD' }}
                                        </div>
                                        <small class="text-muted">ID: {{ $rangeDate->id }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                            <br>
                            No range dates scheduled yet
                        </p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ranges.toggle-active', $content['range']) }}"
                          method="POST"
                          class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-{{ $content['range']->is_active ? 'warning' : 'success' }} btn-block">
                            <i class="fas fa-{{ $content['range']->is_active ? 'ban' : 'check' }}"></i>
                            {{ $content['range']->is_active ? 'Deactivate' : 'Activate' }} Range
                        </button>
                    </form>

                    <button type="button"
                            class="btn btn-danger btn-block"
                            data-toggle="modal"
                            data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete Range
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Confirm Deletion</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to delete this range?</strong></p>
                <p>Range: <strong>{{ $content['range']->name }}</strong></p>
                <p>This action cannot be undone and will also delete:</p>
                <ul>
                    <li>{{ $content['range']->RangeDates->count() }} associated range dates</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.ranges.destroy', $content['range']) }}"
                      method="POST"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
