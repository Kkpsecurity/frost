@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Create New Course
                    </h3>
                </div>

                <form action="{{ route('admin.courses.management.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Course Title <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title') }}"
                                           maxlength="64"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 64 characters</small>
                                </div>

                                <div class="form-group">
                                    <label for="title_long">Long Title</label>
                                    <input type="text"
                                           class="form-control @error('title_long') is-invalid @enderror"
                                           id="title_long"
                                           name="title_long"
                                           value="{{ old('title_long') }}">
                                    @error('title_long')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional extended course title</small>
                                </div>

                                <div class="form-group">
                                    <label for="price">Course Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price"
                                               name="price"
                                               value="{{ old('price', '0.00') }}"
                                               min="0"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Course price in USD</small>
                                </div>
                            </div>

                            <!-- Course Configuration -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exam_id">Associated Exam <span class="text-danger">*</span></label>
                                    <select class="form-control @error('exam_id') is-invalid @enderror"
                                            id="exam_id"
                                            name="exam_id"
                                            required>
                                        <option value="">Select an exam...</option>
                                        @foreach(\App\Models\Exam::all() as $exam)
                                            <option value="{{ $exam->id }}"
                                                    @if(old('exam_id') == $exam->id) selected @endif>
                                                {{ $exam->admin_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select the exam this course prepares for</small>
                                </div>

                                <div class="form-group">
                                    <label for="eq_spec_id">Exam Question Spec <span class="text-danger">*</span></label>
                                    <select class="form-control @error('eq_spec_id') is-invalid @enderror"
                                            id="eq_spec_id"
                                            name="eq_spec_id"
                                            required>
                                        <option value="">Select a question spec...</option>
                                        @foreach(\App\Models\ExamQuestionSpec::all() as $spec)
                                            <option value="{{ $spec->id }}"
                                                    @if(old('eq_spec_id') == $spec->id) selected @endif>
                                                {{ $spec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eq_spec_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Question specification for this course</small>
                                </div>

                                <div class="form-group">
                                    <label for="zoom_creds_id">Zoom Credentials <span class="text-danger">*</span></label>
                                    <select class="form-control @error('zoom_creds_id') is-invalid @enderror"
                                            id="zoom_creds_id"
                                            name="zoom_creds_id"
                                            required>
                                        <option value="">Select zoom credentials...</option>
                                        @foreach(\App\Models\ZoomCreds::all() as $creds)
                                            <option value="{{ $creds->id }}"
                                                    @if(old('zoom_creds_id') == $creds->id) selected @endif>
                                                {{ $creds->zoom_email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zoom_creds_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Zoom account for course meetings</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="needs_range"
                                               name="needs_range"
                                               value="1"
                                               @if(old('needs_range')) checked @endif>
                                        <label class="custom-control-label" for="needs_range">
                                            Needs Range Training
                                        </label>
                                    </div>
                                    @error('needs_range')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Check if this course requires range training</small>
                                </div>
                            </div>
                        </div>

                        <!-- Course Creation Information -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Course Creation</h5>
                                    <p>Fill out all required fields marked with <span class="text-danger">*</span> to create a new course.</p>
                                    <p>Once created, the course will be active by default and you can add course units and lessons.</p>
                                    <p>The total course duration will be automatically calculated based on the course units you add.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Create Course
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .custom-control-label {
            font-weight: 400;
        }
        .form-text {
            font-size: 0.875em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-format price field
            $('#price').on('input', function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    $(this).val(value.toFixed(2));
                }
            });

            // Form validation feedback
            $('form').on('submit', function() {
                let isValid = true;

                // Check required fields
                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    toastr.error('Please fill out all required fields.');
                    return false;
                }

                // Show loading state
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            });
        });
    </script>
@stop
