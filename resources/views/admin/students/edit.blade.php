@extends('adminlte::page')

@section('title', 'Edit Student')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Student</h1>
        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Details
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Information</h3>
                </div>
                <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
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
                            <label for="fname">First Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('fname') is-invalid @enderror"
                                   id="fname"
                                   name="fname"
                                   value="{{ old('fname', $student->fname) }}"
                                   required>
                            @error('fname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lname">Last Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('lname') is-invalid @enderror"
                                   id="lname"
                                   name="lname"
                                   value="{{ old('lname', $student->lname) }}"
                                   required>
                            @error('lname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $student->email) }}"
                                   required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <select class="form-control @error('is_active') is-invalid @enderror"
                                    id="is_active"
                                    name="is_active">
                                <option value="1" {{ old('is_active', $student->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $student->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            <strong>Note:</strong> Course enrollment and unit assignments cannot be changed here.
                            Please use the respective management sections.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
