@extends('adminlte::page')

@section('title', 'Course Unit & Lesson Management - Frost Admin')

@section('content_header')
    <x-admin.partials.titlebar
        title="Course Unit & Lesson Management"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Course Unit Management']
        ]"
    />
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-layer-group"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Course Units</span>
                    <span class="info-box-number">{{ $content['stats']['total_course_units'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Units with Lessons</span>
                    <span class="info-box-number">{{ $content['stats']['units_with_lessons'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Empty Units</span>
                    <span class="info-box-number">{{ $content['stats']['empty_units'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avg Hours per Unit</span>
                    <span class="info-box-number">{{ $content['stats']['avg_hours_per_unit'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Actions --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filters & Actions
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" id="filter-form" class="row align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search units or courses..."
                                   value="{{ $content['filters']['search'] ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label for="course" class="form-label">Filter by Course</label>
                            <select id="course" name="course" class="form-control">
                                <option value="">All Courses</option>
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}"
                                            {{ ($content['filters']['course'] ?? '') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="has_lessons" class="form-label">Lesson Status</label>
                            <select id="has_lessons" name="has_lessons" class="form-control">
                                <option value="">All Course Units</option>
                                <option value="yes" {{ ($content['filters']['has_lessons'] ?? '') === 'yes' ? 'selected' : '' }}>
                                    With Lessons
                                </option>
                                <option value="no" {{ ($content['filters']['has_lessons'] ?? '') === 'no' ? 'selected' : '' }}>
                                    Empty Units
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Action Buttons --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.lessons.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Create New Lesson
                                </a>
                                <button type="button" class="btn btn-danger" id="bulk-delete-btn" style="display: none;">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                                <button type="button" class="btn btn-info" id="bulk-assign-btn" style="display: none;">
                                    <i class="fas fa-link"></i> Assign Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Course Units Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i>
                        Course Units with Lessons ({{ $content['course_units']->total() }} total)
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="select-all">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="select-none">
                                <i class="fas fa-square"></i> Select None
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="master-checkbox" class="form-check-input">
                                </th>
                                <th>Course & Unit</th>
                                <th>Day/Ordering</th>
                                <th>Total Lessons</th>
                                <th>Total Minutes</th>
                                <th>Lesson Details</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($content['course_units'] as $courseUnit)
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               class="unit-checkbox form-check-input"
                                               value="{{ $courseUnit->id }}"
                                               data-unit-title="{{ $courseUnit->title }}">
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-primary">{{ $courseUnit->Course->title }}</strong>
                                            <br>
                                            <span class="text-dark">{{ $courseUnit->title }}</span>
                                            @if($courseUnit->admin_title)
                                                <br><small class="text-muted">{{ $courseUnit->admin_title }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="badge badge-primary badge-lg">
                                                Day {{ $courseUnit->ordering }}
                                            </span>
                                            <br><small class="text-muted">Order: {{ $courseUnit->ordering }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($courseUnit->CourseUnitLessons->count() > 0)
                                            <span class="badge badge-success badge-lg">
                                                {{ $courseUnit->CourseUnitLessons->count() }} lessons
                                            </span>
                                        @else
                                            <span class="badge badge-warning">No lessons</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($courseUnit->CourseUnitLessons->sum('progress_minutes') > 0)
                                            <div>
                                                <strong>{{ $courseUnit->CourseUnitLessons->sum('progress_minutes') }} min</strong>
                                                <br><small class="text-muted">{{ round($courseUnit->CourseUnitLessons->sum('progress_minutes') / 60, 1) }} hours</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No time</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($courseUnit->CourseUnitLessons->count() > 0)
                                            <div class="lesson-list">
                                                @foreach($courseUnit->CourseUnitLessons->sortBy('ordering')->take(3) as $cul)
                                                    <div class="lesson-item mb-1">
                                                        <small class="badge badge-outline-secondary">
                                                            {{ $cul->ordering }}. {{ Str::limit($cul->Lesson->title, 25) }}
                                                        </small>
                                                        <span class="text-muted small">({{ $cul->progress_minutes }}min)</span>
                                                    </div>
                                                @endforeach
                                                @if($courseUnit->CourseUnitLessons->count() > 3)
                                                    <small class="text-muted">
                                                        ...and {{ $courseUnit->CourseUnitLessons->count() - 3 }} more
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No lessons assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.lessons.units.manage', $courseUnit) }}"
                                               class="btn btn-primary btn-sm"
                                               title="Manage Lessons">
                                                <i class="fas fa-cogs"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.manage.units', $courseUnit->Course) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Course Units">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-secondary btn-sm add-lesson-btn"
                                                    data-unit-id="{{ $courseUnit->id }}"
                                                    data-unit-title="{{ $courseUnit->title }}"
                                                    title="Add Lesson">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-warning btn-sm reorder-btn"
                                                    data-unit-id="{{ $courseUnit->id }}"
                                                    title="Reorder Lessons">
                                                <i class="fas fa-sort"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                                            <h5>No course units found</h5>
                                            <p class="text-muted">
                                                @if(request()->hasAny(['search', 'course']))
                                                    Try adjusting your filters or
                                                    <a href="{{ route('admin.lessons.index') }}">clear all filters</a>.
                                                @else
                                                    Course units will appear here when courses are created with units.
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($content['course_units']->hasPages())
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="text-sm text-muted mb-0">
                                    Showing {{ $content['course_units']->firstItem() }} to {{ $content['course_units']->lastItem() }}
                                    of {{ $content['course_units']->total() }} course units
                                </p>
                            </div>
                            <div class="col-md-6">
                                {{ $content['course_units']->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bulk Delete Modal --}}
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkDeleteModalLabel">
                        <i class="fas fa-trash text-danger"></i> Confirm Bulk Delete
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the following lessons?</p>
                    <ul id="bulk-delete-list" class="list-unstyled"></ul>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone. Lessons with exam questions will not be deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.lessons.bulk.delete') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="lesson_ids" id="bulk-delete-ids">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Selected Lessons
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Assign Modal --}}
    <div class="modal fade" id="bulkAssignModal" tabindex="-1" role="dialog" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkAssignModalLabel">
                        <i class="fas fa-link text-info"></i> Bulk Assign to Course Unit
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.lessons.bulk.assign') }}">
                    @csrf
                    <div class="modal-body">
                        <p>Assign the following lessons to a course unit:</p>
                        <ul id="bulk-assign-list" class="list-unstyled mb-3"></ul>

                        <div class="form-group">
                            <label for="bulk-assign-course">Select Course:</label>
                            <select id="bulk-assign-course" class="form-control" required>
                                <option value="">Choose a course...</option>
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="bulk-assign-unit">Select Course Unit:</label>
                            <select id="bulk-assign-unit" name="course_unit_id" class="form-control" required disabled>
                                <option value="">First select a course...</option>
                            </select>
                        </div>

                        <input type="hidden" name="lesson_ids" id="bulk-assign-ids">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-link"></i> Assign Selected Lessons
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Checkbox management
            const masterCheckbox = $('#master-checkbox');
            const lessonCheckboxes = $('.lesson-checkbox');
            const bulkDeleteBtn = $('#bulk-delete-btn');
            const bulkAssignBtn = $('#bulk-assign-btn');

            // Master checkbox functionality
            masterCheckbox.on('change', function() {
                lessonCheckboxes.prop('checked', $(this).is(':checked'));
                updateBulkButtons();
            });

            // Individual checkbox functionality
            lessonCheckboxes.on('change', function() {
                updateBulkButtons();
                updateMasterCheckbox();
            });

            // Select all/none buttons
            $('#select-all').on('click', function() {
                lessonCheckboxes.prop('checked', true);
                masterCheckbox.prop('checked', true);
                updateBulkButtons();
            });

            $('#select-none').on('click', function() {
                lessonCheckboxes.prop('checked', false);
                masterCheckbox.prop('checked', false);
                updateBulkButtons();
            });

            function updateMasterCheckbox() {
                const totalCheckboxes = lessonCheckboxes.length;
                const checkedCheckboxes = lessonCheckboxes.filter(':checked').length;

                if (checkedCheckboxes === 0) {
                    masterCheckbox.prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    masterCheckbox.prop('indeterminate', false).prop('checked', true);
                } else {
                    masterCheckbox.prop('indeterminate', true);
                }
            }

            function updateBulkButtons() {
                const checkedCheckboxes = lessonCheckboxes.filter(':checked');
                if (checkedCheckboxes.length > 0) {
                    bulkDeleteBtn.show();
                    bulkAssignBtn.show();
                } else {
                    bulkDeleteBtn.hide();
                    bulkAssignBtn.hide();
                }
            }

            // Bulk delete functionality
            bulkDeleteBtn.on('click', function() {
                const checkedCheckboxes = lessonCheckboxes.filter(':checked');
                const lessonIds = [];
                const lessonTitles = [];

                checkedCheckboxes.each(function() {
                    lessonIds.push($(this).val());
                    lessonTitles.push($(this).data('lesson-title'));
                });

                $('#bulk-delete-ids').val(JSON.stringify(lessonIds));
                $('#bulk-delete-list').html(lessonTitles.map(title => `<li><i class="fas fa-book text-muted mr-2"></i>${title}</li>`).join(''));
                $('#bulkDeleteModal').modal('show');
            });

            // Bulk assign functionality
            bulkAssignBtn.on('click', function() {
                const checkedCheckboxes = lessonCheckboxes.filter(':checked');
                const lessonIds = [];
                const lessonTitles = [];

                checkedCheckboxes.each(function() {
                    lessonIds.push($(this).val());
                    lessonTitles.push($(this).data('lesson-title'));
                });

                $('#bulk-assign-ids').val(JSON.stringify(lessonIds));
                $('#bulk-assign-list').html(lessonTitles.map(title => `<li><i class="fas fa-book text-muted mr-2"></i>${title}</li>`).join(''));
                $('#bulkAssignModal').modal('show');
            });

            // Course selection for bulk assign
            $('#bulk-assign-course').on('change', function() {
                const courseId = $(this).val();
                const unitSelect = $('#bulk-assign-unit');

                if (courseId) {
                    // Load course units via AJAX
                    $.get(`/admin/lessons/api/course/${courseId}/units`)
                        .done(function(units) {
                            unitSelect.empty().append('<option value="">Select a course unit...</option>');
                            units.forEach(function(unit) {
                                unitSelect.append(`<option value="${unit.id}">${unit.title}</option>`);
                            });
                            unitSelect.prop('disabled', false);
                        })
                        .fail(function() {
                            alert('Failed to load course units. Please try again.');
                            unitSelect.empty().append('<option value="">Failed to load units</option>').prop('disabled', true);
                        });
                } else {
                    unitSelect.empty().append('<option value="">First select a course...</option>').prop('disabled', true);
                }
            });

            // Individual delete functionality
            $('.delete-lesson').on('click', function() {
                const lessonId = $(this).data('lesson-id');
                const lessonTitle = $(this).data('lesson-title');

                if (confirm(`Are you sure you want to delete the lesson "${lessonTitle}"?\n\nThis action cannot be undone.`)) {
                    // Create a form and submit it
                    const form = $('<form>', {
                        method: 'POST',
                        action: `/admin/lessons/${lessonId}`
                    });

                    form.append($('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

                    $('body').append(form);
                    form.submit();
                }
            });

            // Auto-submit filter form on change (with debounce for search)
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filter-form').submit();
                }, 500);
            });

            $('#course, #has_units').on('change', function() {
                $('#filter-form').submit();
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .empty-state {
            padding: 2rem;
        }

        .info-box-number {
            font-size: 1.8rem;
        }

        .unit-checkbox {
            transform: scale(1.2);
        }

        #master-checkbox {
            transform: scale(1.3);
        }

        .table th a {
            color: inherit;
            text-decoration: none;
        }

        .table th a:hover {
            color: #007bff;
        }

        .badge-outline-secondary {
            color: #6c757d;
            border: 1px solid #6c757d;
            background: transparent;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .lesson-list {
            max-height: 100px;
            overflow-y: auto;
        }

        .lesson-item {
            display: flex;
            justify-content: between;
            align-items: center;
            flex-wrap: wrap;
        }

        .lesson-item .badge {
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }

        @media (max-width: 768px) {
            .btn-group {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group .btn {
                flex: 1;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .lesson-list {
                max-height: 80px;
            }
        }
    </style>
@endsection
