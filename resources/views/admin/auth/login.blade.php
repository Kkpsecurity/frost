@extends('layouts.admin-auth')

@section('title', 'Admin Login - Frost')
@section('subtitle', 'Sign in to admin panel')

@section('content')
<form method="POST" action="{{ route('admin.login') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">Admin Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
               name="password" required autocomplete="current-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Remember me
        </label>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In to Admin Panel
    </button>

    <div class="auth-links">
        <a href="{{ route('home') }}">← Back to main site</a>
    </div>
</form>
@endsection
