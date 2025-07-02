<form method="POST" action="{{ route('register') }}" id="register-form" class="login-form" role="form">
    @csrf

    <div class="form-group">
        <label class="text-white" for="fname">First Name</label>
        <input id="fname" type="text" class="form-control @error('fname') is-invalid @enderror" name="fname" value="{{ old('fname') }}" required autocomplete="fname" autofocus>
        @error('fname')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label class="text-white" for="lname">Last Name</label>
        <input id="lname" type="text" class="form-control @error('lname') is-invalid @enderror" name="lname" value="{{ old('lname') }}" required autocomplete="lname">
        @error('lname')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label class="text-white" for="email">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label class="text-white" for="password">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label class="text-white" for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="privacy_agree" name="privacy_agree" required>
        <label class="form-check-label text-white" for="privacy_agree">
            By submitting this form, you agree to our 
            <a href="{{ route('pages', 'privacy') }}" target="_blank" class="text-info">Privacy Policy</a>
        </label>
        @error('privacy_agree')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="d-flex justify-content-start align-items-center p-2 text-white" style="height: auto;">
                    <div class="register-text">
                        Already have an account?
                        <a class="btn btn-link text-white" href="{{ url('/login') }}">Login</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary m-2 login-btn">
                    Create
                </button>
            </div>
        </div>
    </div>
</form>
