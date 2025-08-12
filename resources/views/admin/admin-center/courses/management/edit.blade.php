@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Edit Course: {{ $content['course']->title }}
                    </h3>
                    <div class="card-tools">
                        @if($content['course']->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Archived</span>
                        @endif
                    </div>
                </div>

                <form action="{{ route('admin.courses.management.update', $content['course']) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                           value="{{ old('title', $content['course']->title) }}"
                                           maxlength="64"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="title_long">Long Title</label>
                                    <input type="text"
                                           class="form-control @error('title_long') is-invalid @enderror"
                                           id="title_long"
                                           name="title_long"
                                           value="{{ old('title_long', $content['course']->title_long) }}">
                                    @error('title_long')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                               value="{{ old('price', $content['course']->price) }}"
                                               min="0"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                                    @if(old('exam_id', $content['course']->exam_id) == $exam->id) selected @endif>
                                                {{ $exam->admin_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                                    @if(old('eq_spec_id', $content['course']->eq_spec_id) == $spec->id) selected @endif>
                                                {{ $spec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eq_spec_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                                    @if(old('zoom_creds_id', $content['course']->zoom_creds_id) == $creds->id) selected @endif>
                                                {{ $creds->zoom_email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zoom_creds_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="needs_range"
                                               name="needs_range"
                                               value="1"
                                               @if(old('needs_range', $content['course']->needs_range)) checked @endif>
                                        <label class="custom-control-label" for="needs_range">
                                            Needs Range Training
                                        </label>
                                    </div>
                                    @error('needs_range')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Course Status Information -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Course Information</h5>
                                    <p><strong>Current Status:</strong>
                                        @if($content['course']->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Archived</span>
                                        @endif
                                    </p>
                                    <p><strong>Total Duration:</strong> {{ $content['course']->total_minutes }} minutes</p>
                                    <p><strong>Course Type:</strong> {{ $content['course']->getCourseType() }} Course</p>
                                    <p><strong>Created:</strong> {{ $content['course']->created_at }}</p>
                                    <p><strong>Last Updated:</strong> {{ $content['course']->updated_at }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ route('admin.courses.management.show', $content['course']) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Course
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
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Add any client-side validation or form enhancements here

            // Auto-format price field
            $('#price').on('input', function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    $(this).val(value.toFixed(2));
                }
            });
        });
    </script>
@stop
