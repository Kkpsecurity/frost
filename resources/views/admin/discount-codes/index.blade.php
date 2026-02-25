@extends('adminlte::page')

@section('title', 'Client Discount Codes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tags mr-2"></i> Client Discount Codes</h1>
        <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Code
        </a>
    </div>
@stop

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if ($errors->has('delete'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('delete') }}
        </div>
    @endif

    {{-- Stats --}}
    @if (isset($content['stats']))
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-tags"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Codes</span>
                        <span class="info-box-number">{{ $content['stats']['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active</span>
                        <span class="info-box-number">{{ $content['stats']['active'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expired</span>
                        <span class="info-box-number">{{ $content['stats']['expired'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Clients</span>
                        <span class="info-box-number">{{ $content['stats']['clients'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card card-outline card-secondary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.discount-codes.index') }}" method="GET" class="form-inline flex-wrap">
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Client</label>
                    <select name="client" class="form-control form-control-sm">
                        <option value="">All Clients</option>
                        @foreach ($content['client_list'] as $client)
                            <option value="{{ $client }}"
                                {{ $content['filters']['client'] === $client ? 'selected' : '' }}>
                                {{ $client }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Course</label>
                    <select name="course_id" class="form-control form-control-sm">
                        <option value="">All Courses</option>
                        @foreach ($content['courses'] as $course)
                            <option value="{{ $course->id }}"
                                {{ $content['filters']['course_id'] == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="all" {{ $content['filters']['status'] === 'all' ? 'selected' : '' }}>All
                        </option>
                        <option value="active" {{ $content['filters']['status'] === 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="expired" {{ $content['filters']['status'] === 'expired' ? 'selected' : '' }}>
                            Expired</option>
                        <option value="unlimited" {{ $content['filters']['status'] === 'unlimited' ? 'selected' : '' }}>
                            Unlimited</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-sm btn-primary mr-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Discount Codes</h3>
            <div class="card-tools">
                <span class="badge badge-secondary">{{ $content['discount_codes']->total() }} results</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Client</th>
                            <th>Course</th>
                            <th>Discount</th>
                            <th>Max Uses</th>
                            <th class="text-center">Used</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content['discount_codes'] as $dc)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.discount-codes.show', $dc->id) }}"
                                        class="font-weight-bold text-monospace">
                                        {{ $dc->code }}
                                    </a>
                                    @if ($dc->AppliesFree())
                                        <span class="badge badge-success ml-1">FREE</span>
                                    @endif
                                </td>
                                <td>{{ $dc->client ?? '—' }}</td>
                                <td>
                                    @if ($dc->Course)
                                        <small>{{ $dc->Course->title }}</small>
                                    @else
                                        <span class="text-muted">All courses</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($dc->percent)
                                        <span class="badge badge-info">{{ $dc->percent }}% off</span>
                                    @elseif($dc->set_price !== null)
                                        <span class="badge badge-primary">${{ number_format($dc->set_price, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $dc->max_count ?? '∞' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $dc->times_used > 0 ? 'success' : 'secondary' }}">
                                        {{ $dc->times_used }}
                                    </span>
                                </td>
                                <td>
                                    @if ($dc->expires_at)
                                        @if ($dc->IsExpired())
                                            <span class="text-danger"
                                                title="{{ $dc->expires_at->format('M d, Y g:i A') }}">
                                                <i class="fas fa-times-circle"></i>
                                                {{ $dc->expires_at->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-success"
                                                title="{{ $dc->expires_at->format('M d, Y g:i A') }}">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $dc->expires_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.discount-codes.show', $dc->id) }}" class="btn btn-info"
                                            title="View usage">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.discount-codes.edit', $dc->id) }}"
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.discount-codes.usage.csv', $dc->id) }}"
                                            class="btn btn-secondary" title="Export CSV">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if ($dc->times_used === 0)
                                            <form action="{{ route('admin.discount-codes.destroy', $dc->id) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Delete code {{ $dc->code }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                    No discount codes found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($content['discount_codes']->hasPages())
            <div class="card-footer">
                {{ $content['discount_codes']->links() }}
            </div>
        @endif
    </div>

@stop
