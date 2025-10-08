<form method="POST" action="{{ url('password/reset') }}" class="text-white">
    @csrf

    <input type="hidden" name="token" value="{{ request()->route('token') }}">

    <div class="row mb-3">
        <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>

        <div class="col-md-6">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', request()->get('email')) }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>

        <div class="col-md-6">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="new_password" class="col-md-4 col-form-label text-md-end">{{ __('Confirm New Password') }}</label>

        <div class="col-md-6">
            <input id="new_password" type="password" class="form-control" name="new_password" required autocomplete="new-_password">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Reset Password') }}
            </button>
        </div>
    </div>
</form>
