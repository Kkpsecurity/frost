@extends('adminlte::page')

@section('title', 'Create Discount Code')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus-circle mr-2"></i> Create Discount Code</h1>
        <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Codes
        </a>
    </div>
@stop

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.discount-codes.store') }}" method="POST">
        @csrf

        <div class="row">
            {{-- Left column --}}
            <div class="col-md-7">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Code Details</h3>
                    </div>
                    <div class="card-body">

                        {{-- Code --}}
                        <div class="form-group">
                            <label for="code">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code"
                                class="form-control text-uppercase @error('code') is-invalid @enderror"
                                value="{{ old('code') }}" maxlength="32" placeholder="e.g. CLIENT2025" required>
                            <small class="form-text text-muted">Max 32 characters. Must be unique.</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Client --}}
                        <div class="form-group">
                            <label for="client">Client / Sponsor</label>
                            <input type="text" name="client" id="client"
                                class="form-control @error('client') is-invalid @enderror" value="{{ old('client') }}"
                                maxlength="32" placeholder="e.g. Acme Corp">
                            <small class="form-text text-muted">Optional. Identifies whose code this is.</small>
                            @error('client')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Course --}}
                        <div class="form-group">
                            <label for="course_id">Restrict to Course</label>
                            <select name="course_id" id="course_id"
                                class="form-control @error('course_id') is-invalid @enderror">
                                <option value="">— All Courses —</option>
                                @foreach ($content['courses'] as $course)
                                    <option value="{{ $course->id }}"
                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Leave blank to allow on any course.</small>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right column --}}
            <div class="col-md-5">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Discount Type</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Set either a fixed price <strong>or</strong> a percentage — not both.
                        </p>

                        {{-- Set Price --}}
                        <div class="form-group">
                            <label for="set_price">Fixed Price ($)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="set_price" id="set_price"
                                    class="form-control @error('set_price') is-invalid @enderror"
                                    value="{{ old('set_price') }}" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Enter 0.00 to make the course free.</small>
                            @error('set_price')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Percent --}}
                        <div class="form-group">
                            <label for="percent">Percent Off (%)</label>
                            <div class="input-group">
                                <input type="number" name="percent" id="percent"
                                    class="form-control @error('percent') is-invalid @enderror"
                                    value="{{ old('percent') }}" min="1" max="100" placeholder="e.g. 10">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            @error('percent')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Limits &amp; Expiry</h3>
                    </div>
                    <div class="card-body">

                        {{-- Max Count --}}
                        <div class="form-group">
                            <label for="max_count">Max Uses</label>
                            <input type="number" name="max_count" id="max_count"
                                class="form-control @error('max_count') is-invalid @enderror"
                                value="{{ old('max_count') }}" min="1" placeholder="Leave blank for unlimited">
                            @error('max_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Expires At --}}
                        <div class="form-group">
                            <label for="expires_at">Expiry Date</label>
                            <input type="datetime-local" name="expires_at" id="expires_at"
                                class="form-control @error('expires_at') is-invalid @enderror"
                                value="{{ old('expires_at') }}">
                            <small class="form-text text-muted">Leave blank for no expiry.</small>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Code
                        </button>
                        <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary ml-2">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </form>

@stop

@section('js')
    <script>
        // Auto-uppercase the code field
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Prevent both set_price and percent being filled
        ['set_price', 'percent'].forEach(function(id) {
            document.getElementById(id).addEventListener('input', function() {
                var other = id === 'set_price' ? 'percent' : 'set_price';
                if (this.value !== '') {
                    document.getElementById(other).value = '';
                    document.getElementById(other).disabled = true;
                } else {
                    document.getElementById(other).disabled = false;
                }
            });
        });
    </script>
@stop
