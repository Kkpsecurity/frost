@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-tasks"></i>
            {{ $content['title'] }}
        </h1>
        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Lessons
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Course Unit Information -->
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Course Unit Details
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Course:</th>
                                <td>{{ $content['courseUnit']->Course->title }}</td>
                            </tr>
                            <tr>
                                <th>Unit:</th>
                                <td>{{ $content['courseUnit']->title }}</td>
                            </tr>
                            <tr>
                                <th>Day:</th>
                                <td>{{ $content['courseUnit']->day ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Ordering:</th>
                                <td>{{ $content['courseUnit']->ordering ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Current Lessons:</th>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $content['assignedLessons']->count() }} lessons
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Current Stats
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            $totalMinutes = $content['assignedLessons']->sum('progress_minutes');
                            $totalSeconds = $content['assignedLessons']->sum('instr_seconds');
                        @endphp
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Time</span>
                                <span class="info-box-number">
                                    {{ $totalMinutes }}m {{ $totalSeconds }}s
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lesson Management -->
            <div class="col-md-8">
                <form method="POST" action="{{ route('admin.lessons.units.update', $content['courseUnit']) }}" id="lesson-form">
                    @csrf

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i>
                                Assigned Lessons
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-success" id="add-lesson">
                                    <i class="fas fa-plus"></i>
                                    Add Lesson
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="lessons-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Lesson</th>
                                            <th style="width: 120px;">Minutes</th>
                                            <th style="width: 120px;">Seconds</th>
                                            <th style="width: 100px;">Order</th>
                                            <th style="width: 80px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lessons-tbody">
                                        @foreach($content['assignedLessons'] as $index => $assignment)
                                        <tr data-index="{{ $index }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <select name="lessons[{{ $index }}][lesson_id]" class="form-control form-control-sm lesson-select" required>
                                                    @foreach($content['availableLessons'] as $lesson)
                                                        <option value="{{ $lesson->id }}"
                                                            {{ $assignment->lesson_id == $lesson->id ? 'selected' : '' }}>
                                                            {{ $lesson->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="lessons[{{ $index }}][progress_minutes]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $assignment->progress_minutes }}"
                                                       min="0" max="9999" required>
                                            </td>
                                            <td>
                                                <input type="number" name="lessons[{{ $index }}][instr_seconds]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $assignment->instr_seconds }}"
                                                       min="0" max="999999" required>
                                            </td>
                                            <td>
                                                <input type="number" name="lessons[{{ $index }}][ordering]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $assignment->ordering }}"
                                                       min="1" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-lesson">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($content['assignedLessons']->isEmpty())
                                <div class="text-center text-muted py-4" id="no-lessons-message">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p>No lessons assigned to this course unit yet.</p>
                                    <p>Click "Add Lesson" to start adding lessons.</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Drag rows to reorder lessons
                                    </small>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden template row for new lessons -->
    <template id="lesson-row-template">
        <tr data-index="__INDEX__">
            <td class="row-number">__ROW_NUMBER__</td>
            <td>
                <select name="lessons[__INDEX__][lesson_id]" class="form-control form-control-sm lesson-select" required>
                    <option value="">Select a lesson...</option>
                    @foreach($content['availableLessons'] as $lesson)
                        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="lessons[__INDEX__][progress_minutes]"
                       class="form-control form-control-sm"
                       value="0" min="0" max="9999" required>
            </td>
            <td>
                <input type="number" name="lessons[__INDEX__][instr_seconds]"
                       class="form-control form-control-sm"
                       value="0" min="0" max="999999" required>
            </td>
            <td>
                <input type="number" name="lessons[__INDEX__][ordering]"
                       class="form-control form-control-sm"
                       value="__ORDER__" min="1" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-lesson">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@stop

@section('js')
<script>
$(document).ready(function() {
    let lessonIndex = {{ $content['assignedLessons']->count() }};

    // Add lesson functionality
    $('#add-lesson').click(function() {
        const template = $('#lesson-row-template').html();
        const newIndex = lessonIndex++;
        const newOrder = $('#lessons-tbody tr').length + 1;

        const newRow = template
            .replace(/__INDEX__/g, newIndex)
            .replace(/__ROW_NUMBER__/g, newOrder)
            .replace(/__ORDER__/g, newOrder);

        $('#lessons-tbody').append(newRow);
        $('#no-lessons-message').hide();
        updateRowNumbers();
    });

    // Remove lesson functionality
    $(document).on('click', '.remove-lesson', function() {
        $(this).closest('tr').remove();
        updateRowNumbers();

        if ($('#lessons-tbody tr').length === 0) {
            $('#no-lessons-message').show();
        }
    });

    // Update row numbers
    function updateRowNumbers() {
        $('#lessons-tbody tr').each(function(index) {
            $(this).find('.row-number').text(index + 1);
            $(this).find('input[name$="[ordering]"]').val(index + 1);
        });
    }

    // Make table sortable
    $('#lessons-tbody').sortable({
        handle: 'td',
        cursor: 'move',
        update: function() {
            updateRowNumbers();
        }
    });

    // Form validation
    $('#lesson-form').submit(function(e) {
        let valid = true;
        const lessonIds = [];

        // Check for duplicate lessons
        $('.lesson-select').each(function() {
            const lessonId = $(this).val();
            if (lessonId) {
                if (lessonIds.includes(lessonId)) {
                    alert('Error: Duplicate lessons are not allowed.');
                    valid = false;
                    return false;
                }
                lessonIds.push(lessonId);
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
});
</script>
@stop

@section('css')
<style>
#lessons-tbody tr {
    cursor: move;
}

#lessons-tbody tr:hover {
    background-color: #f8f9fa;
}

.ui-sortable-helper {
    background-color: #fff3cd !important;
}

#no-lessons-message {
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    border: 2px dashed #dee2e6;
}
</style>
@stop
