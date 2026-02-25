@extends('adminlte::page')

@section('title', 'Discount Code: ' . $content['discount_code']->code)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tag mr-2"></i> <span class="text-monospace">{{ $content['discount_code']->code }}</span></h1>
        <div>
            <a href="{{ route('admin.discount-codes.edit', $content['discount_code']->id) }}" class="btn btn-warning mr-1">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.discount-codes.usage.csv', $content['discount_code']->id) }}"
                class="btn btn-secondary mr-1">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @php
        $dc = $content['discount_code'];
        $timesUsed = $content['orders']->count();
    @endphp

    <div class="row">

        {{-- Code detail card --}}
        <div class="col-md-5">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Code Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Code</dt>
                        <dd class="col-sm-7 font-weight-bold text-monospace">{{ $dc->code }}</dd>

                        <dt class="col-sm-5">Client</dt>
                        <dd class="col-sm-7">{{ $dc->client ?? '—' }}</dd>

                        <dt class="col-sm-5">Course</dt>
                        <dd class="col-sm-7">
                            @if ($dc->Course)
                                {{ $dc->Course->title }}
                            @else
                                <span class="text-muted">All courses</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Discount</dt>
                        <dd class="col-sm-7">
                            @if ($dc->AppliesFree())
                                <span class="badge badge-success">FREE ($0.00)</span>
                            @elseif($dc->set_price !== null)
                                <span class="badge badge-primary">Fixed: ${{ number_format($dc->set_price, 2) }}</span>
                            @elseif($dc->percent)
                                <span class="badge badge-info">{{ $dc->percent }}% off</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Max Uses</dt>
                        <dd class="col-sm-7">
                            @if ($dc->max_count)
                                {{ $timesUsed }} / {{ $dc->max_count }}
                                @if ($timesUsed >= $dc->max_count)
                                    <span class="badge badge-danger ml-1">MAXED</span>
                                @endif
                            @else
                                {{ $timesUsed }} / <span class="text-muted">∞ unlimited</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">
                            @if ($dc->IsExpired())
                                <span class="badge badge-danger">Expired</span>
                            @elseif($dc->max_count && $timesUsed >= $dc->max_count)
                                <span class="badge badge-warning">Exhausted</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Expires</dt>
                        <dd class="col-sm-7">
                            @if ($dc->expires_at)
                                {{ $dc->expires_at->format('M d, Y g:i A') }} ET
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Created</dt>
                        <dd class="col-sm-7">{{ $dc->created_at->format('M d, Y') }}</dd>

                        @if ($dc->uuid)
                            <dt class="col-sm-5">UUID</dt>
                            <dd class="col-sm-7"><small class="text-monospace">{{ $dc->uuid }}</small></dd>
                        @endif
                    </dl>
                </div>

                {{-- Delete action (only if unused) --}}
                @if ($timesUsed === 0)
                    <div class="card-footer">
                        <form action="{{ route('admin.discount-codes.destroy', $dc->id) }}" method="POST"
                            onsubmit="return confirm('Permanently delete code \'{{ $dc->code }}\'?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete Code
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Usage / students --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Students
                        <span class="badge badge-secondary ml-1">{{ $timesUsed }}</span>
                    </h3>
                    @if ($timesUsed > 0)
                        <div class="card-tools">
                            <a href="{{ route('admin.discount-codes.usage.csv', $dc->id) }}"
                                class="btn btn-sm btn-secondary">
                                <i class="fas fa-download"></i> CSV
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if ($timesUsed > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student</th>
                                        <th class="text-center">Lessons</th>
                                        <th>Created At</th>
                                        <th>Completed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($content['orders'] as $order)
                                        @php
                                            $completedLessons = 0;
                                            if ($order->CourseAuth) {
                                                foreach ($order->CourseAuth->StudentUnits as $su) {
                                                    $completedLessons += $su->StudentLessons
                                                        ->whereNotNull('completed_at')
                                                        ->count();
                                                }
                                            }
                                            $totalLessons = $content['total_lessons_map'][$order->course_id] ?? 0;
                                            $courseComplete = $order->CourseAuth?->completed_at;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if ($order->User)
                                                    <a href="{{ route('admin.students.show', $order->User->id) }}">
                                                        {{ $order->User->fname }} {{ $order->User->lname }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($totalLessons > 0)
                                                    <span
                                                        class="{{ $completedLessons >= $totalLessons ? 'text-success font-weight-bold' : '' }}">
                                                        {{ $completedLessons }} / {{ $totalLessons }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($order->created_at)->tz('America/New_York')->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td>
                                                @if ($courseComplete)
                                                    <small class="text-success">
                                                        {{ \Carbon\Carbon::parse($courseComplete)->tz('America/New_York')->format('Y-m-d H:i') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            This code has not been used yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@stop
