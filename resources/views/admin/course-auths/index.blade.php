@extends('adminlte::page')

@section('title', 'Student Courses')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-graduate mr-2"></i> Student Courses</h1>
    </div>
@stop

@section('content')

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-play-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Enrollments</span>
                    <span class="info-box-number">{{ number_format($content['stats']['active']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Completed</span>
                    <span class="info-box-number">{{ number_format($content['stats']['completed']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Missing DOL Records</span>
                    <span class="info-box-number">{{ number_format($content['stats']['missing_dol']) }}</span>
                </div>
            </div>
        </div>
    </div>

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
            <form action="{{ route('admin.course-auths.index') }}" method="GET" class="form-inline flex-wrap">
                <input type="hidden" name="tab" value="{{ $content['filters']['tab'] }}">

                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Course</label>
                    <select name="course_id" class="form-control form-control-sm">
                        <option value="">All Courses</option>
                        @foreach($content['courses'] as $course)
                            <option value="{{ $course->id }}"
                                {{ $content['filters']['course_id'] == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">Completed From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ $content['filters']['date_from'] ?? '' }}">
                </div>
                <div class="form-group mr-3 mb-2">
                    <label class="mr-1">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ $content['filters']['date_to'] ?? '' }}">
                </div>

                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-sm btn-primary mr-1">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.course-auths.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab system --}}
    <div class="card">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="courseAuthTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $content['filters']['tab'] !== 'dol' ? 'active' : '' }}"
                       id="active-tab"
                       href="{{ route('admin.course-auths.index', array_merge(request()->except('tab', 'active_page'), ['tab' => 'active'])) }}"
                       data-toggle="tab" data-target="#tab-active" role="tab">
                        <i class="fas fa-play-circle mr-1"></i>
                        Active Enrollments
                        <span class="badge badge-success ml-1">{{ $content['active_course_auths']->total() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $content['filters']['tab'] === 'dol' ? 'active' : '' }}"
                       id="dol-tab"
                       href="{{ route('admin.course-auths.index', array_merge(request()->except('tab', 'dol_page'), ['tab' => 'dol'])) }}"
                       data-toggle="tab" data-target="#tab-dol" role="tab">
                        <i class="fas fa-exclamation-triangle mr-1 text-danger"></i>
                        Missing DOL Records
                        @if($content['stats']['missing_dol'] > 0)
                            <span class="badge badge-danger ml-1">{{ $content['missing_dol']->total() }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">

                {{-- ═══════════ TAB 1: ACTIVE ENROLLMENTS ═══════════ --}}
                <div class="tab-pane {{ $content['filters']['tab'] !== 'dol' ? 'active' : '' }}"
                     id="tab-active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Enrolled</th>
                                    <th>Start Date</th>
                                    <th class="text-center">Lessons</th>
                                    <th>Expire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($content['active_course_auths'] as $ca)
                                    @php
                                        $completedLessons = 0;
                                        foreach ($ca->StudentUnits as $su) {
                                            $completedLessons += $su->StudentLessons->whereNotNull('completed_at')->count();
                                        }
                                        $totalLessons = $content['total_lessons_map'][$ca->course_id] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($ca->User)
                                                <a href="{{ route('admin.students.show', $ca->User->id) }}">
                                                    {{ $ca->User->fname }} {{ $ca->User->lname }}
                                                </a>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ca->Course)
                                                <small>{{ $ca->Course->title }}</small>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $ca->created_at->tz('America/New_York')->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($ca->start_date)
                                                <small>{{ \Carbon\Carbon::parse($ca->start_date)->format('M d, Y') }}</small>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($totalLessons > 0)
                                                @php $pct = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0; @endphp
                                                <span title="{{ $pct }}%"
                                                      class="{{ $completedLessons >= $totalLessons ? 'text-success font-weight-bold' : '' }}">
                                                    {{ $completedLessons }} / {{ $totalLessons }}
                                                </span>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ca->expire_date)
                                                @php $expiring = \Carbon\Carbon::parse($ca->expire_date)->isPast(); @endphp
                                                <small class="{{ $expiring ? 'text-danger' : 'text-muted' }}">
                                                    {{ \Carbon\Carbon::parse($ca->expire_date)->format('M d, Y') }}
                                                </small>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No active enrollments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($content['active_course_auths']->hasPages())
                        <div class="p-3">
                            {{ $content['active_course_auths']->links() }}
                        </div>
                    @endif
                </div>

                {{-- ═══════════ TAB 2: MISSING DOL ═══════════ --}}
                <div class="tab-pane {{ $content['filters']['tab'] === 'dol' ? 'active' : '' }}"
                     id="tab-dol" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Completed At</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>DOL Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($content['missing_dol'] as $ca)
                                    <tr>
                                        <td>
                                            <small>{{ $ca->completed_at->tz('America/New_York')->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($ca->User)
                                                <a href="{{ route('admin.students.show', $ca->User->id) }}">
                                                    {{ $ca->User->fname }} {{ $ca->User->lname }}
                                                </a>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ca->Course)
                                                <small>{{ $ca->Course->title }}</small>
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ca->submitted_at)
                                                <span class="badge badge-warning">
                                                    Submitted — awaiting tracking #
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Not Submitted
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                            All completed course auths have DOL records.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($content['missing_dol']->hasPages())
                        <div class="p-3">
                            {{ $content['missing_dol']->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

@stop

@section('js')
<script>
    // Keep the correct tab active when navigating via tab links
    document.addEventListener('DOMContentLoaded', function () {
        var tab = '{{ $content['filters']['tab'] }}';
        if (tab === 'dol') {
            var dolTab = document.getElementById('dol-tab');
            if (dolTab) {
                $('#courseAuthTabs a[data-target="#tab-dol"]').tab('show');
            }
        }

        // Update hidden tab input when switching tabs via Bootstrap
        $('#courseAuthTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).data('target');
            var tabName = target === '#tab-dol' ? 'dol' : 'active';
            $('input[name="tab"]').val(tabName);
        });
    });
</script>
@stop
