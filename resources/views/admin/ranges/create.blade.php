@extends('adminlte::page')

@section('title', 'Create Range')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create New Range</h1>
        <a href="{{ route('admin.ranges.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.ranges.store') }}" method="POST">
        @csrf

        <div class="row">
            <!-- Left Column - Basic Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Range Information</h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Range Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="city">City <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('city') is-invalid @enderror"
                                   id="city"
                                   name="city"
                                   value="{{ old('city') }}"
                                   required>
                            @error('city')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('address') is-invalid @enderror"
                                   id="address"
                                   name="address"
                                   value="{{ old('address') }}"
                                   required>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number"
                                               class="form-control @error('price') is-invalid @enderror"
                                               id="price"
                                               name="price"
                                               step="0.01"
                                               min="0"
                                               value="{{ old('price', '0.00') }}"
                                               required>
                                        @error('price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="times">Available Times</label>
                                    <input type="text"
                                           class="form-control @error('times') is-invalid @enderror"
                                           id="times"
                                           name="times"
                                           value="{{ old('times', '...') }}"
                                           placeholder="e.g., 9AM-5PM">
                                    @error('times')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="appt_only"
                                       name="appt_only"
                                       value="1"
                                       {{ old('appt_only') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="appt_only">
                                    Appointment Only (No walk-ins)
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active (visible to students)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Contact & Details -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Contact Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="inst_name">Instructor Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('inst_name') is-invalid @enderror"
                                   id="inst_name"
                                   name="inst_name"
                                   value="{{ old('inst_name') }}"
                                   required>
                            @error('inst_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="inst_email">Instructor Email</label>
                            <input type="email"
                                   class="form-control @error('inst_email') is-invalid @enderror"
                                   id="inst_email"
                                   name="inst_email"
                                   value="{{ old('inst_email') }}">
                            @error('inst_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="inst_phone">Instructor Phone</label>
                            <input type="text"
                                   class="form-control @error('inst_phone') is-invalid @enderror"
                                   id="inst_phone"
                                   name="inst_phone"
                                   value="{{ old('inst_phone') }}"
                                   placeholder="(123) 456-7890">
                            @error('inst_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Description (HTML)</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="range_html">Additional Details</label>
                            <textarea class="form-control @error('range_html') is-invalid @enderror"
                                      id="range_html"
                                      name="range_html"
                                      rows="8">{{ old('range_html') }}</textarea>
                            @error('range_html')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                HTML allowed. This will be displayed on the range details page.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Range
                </button>
                <a href="{{ route('admin.ranges.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@stop
