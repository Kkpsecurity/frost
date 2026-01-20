@extends('adminlte::page')

@section('title', 'Edit Course')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Course</h1>
        <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Course Information</h3>
                </div>
                <form action="{{ route('admin.courses.management.update', $content['course']->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="title">Course Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $content['course']->title) }}"
                                   required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
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
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number"
                                       step="0.01"
                                       class="form-control @error('price') is-invalid @enderror"
                                       id="price"
                                       name="price"
                                       value="{{ old('price', $content['course']->price) }}"
                                       required>
                                @error('price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <select class="form-control @error('is_active') is-invalid @enderror"
                                    id="is_active"
                                    name="is_active">
                                <option value="1" {{ old('is_active', $content['course']->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $content['course']->is_active) ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('is_active')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            <strong>Note:</strong> Course units and lessons are managed separately.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
