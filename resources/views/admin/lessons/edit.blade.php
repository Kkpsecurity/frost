@extends('adminlte::page')

@section('title', 'Edit Lesson')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Lesson</h1>
        <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Lessons
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.lessons.management.update', $content['lesson']->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column - Basic Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lesson Information</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="title">Lesson Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $content['lesson']->title) }}"
                                   required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="credit_minutes">Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('credit_minutes') is-invalid @enderror"
                                   id="credit_minutes"
                                   name="credit_minutes"
                                   value="{{ old('credit_minutes', $content['lesson']->credit_minutes) }}"
                                   required>
                            @error('credit_minutes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4">{{ old('description', $content['lesson']->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            <strong>Note:</strong> Changes to course unit assignments will affect lesson scheduling.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Course Unit Assignments -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Course Unit Assignments</h3>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @if(isset($content['courses']))
                            @foreach($content['courses'] as $course)
                                <div class="mb-3 pb-3 border-bottom">
                                    <h5 class="text-primary mb-2">
                                        <i class="fas fa-book"></i> {{ $course->title }}
                                    </h5>
                                    @if($course->CourseUnits->count() > 0)
                                        @foreach($course->CourseUnits as $unit)
                                            <div class="custom-control custom-checkbox ml-3 mb-1">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="unit_{{ $unit->id }}"
                                                       name="course_units[]"
                                                       value="{{ $unit->id }}"
                                                       {{ in_array($unit->id, old('course_units', $content['lesson']->CourseUnits->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="unit_{{ $unit->id }}">
                                                    {{ $unit->title }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted ml-3 mb-0">No units available</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No courses available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@stop
