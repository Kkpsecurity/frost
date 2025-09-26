<form action="{{ route('login') }}" method="POST" id="login-form" class="form" role="form">
    @csrf
    <div class="form-group mb-2">
        <label class="text-white" for="email">Email</label>
        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
            placeholder="Please enter your email" required>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group mb-2">
        <label class="text-white" for="password">Password</label>
        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror" placeholder="Please enter a valid Password"
            required>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group check-group d-flex align-items-center justify-content-between">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-white" for="remember">Remember me</label>
        </div>
        <div class="text-right" style="margin-top: -10px">
            <a class="text-white" href="{{ url('/password/reset') }}">Forgot password?</a>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="d-flex  border-bottom justify-content-start align-items-center p-2 text-white" style="height: auto;">
                    <div class="register-text">
                        Don't have an account?
                        <a class="btn btn-link text-white" href="{{ url('/register') }}">Sign up</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-end">
                <button type="submit" id="submit" class="btn login-btn m-2">Login</button>
            </div>
        </div>
    </div>

    <div class="form-group text-center" style="display: none">
        <div class="separetor text-white text-center"><span>Or with Sign</span></div>
        <div class="sign-icon">
            <ul>
                <li><a class="facebook" href="#"><i class="ti-facebook"></i></a></li>
                <li><a class="twitter" href="#"><i class="ti-twitter"></i></a></li>
                <li><a class="google" href="#"><i class="ti-google"></i></a></li>
            </ul>
        </div>
    </div>
</form>
