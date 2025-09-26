<style>
    .card {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.2);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #f1f1f1;
        border-radius: 5px 5px 0 0;
        color: #333;
        font-size: 18px;
        font-weight: 600;
        padding: 15px;
    }

    .card-body {
        padding: 20px;
        height: 100%;
    }

    .form {
        width: 100%;
        box-sizing: border-box;
        height: auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-group input {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        width: 100%;
    }

    .card-footer {
        background-color: #f1f1f1;
        border-radius: 0 0 5px 5px;
        padding: 15px;
    }

    .btn {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        padding: 10px 20px;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn:active {
        background-color: #004085;
    }

    .progress-bar {
        transition: width .6s ease;
    }
</style>
<div class="row password-view">
    <div class="col-lg-12">
        <div class="row align-items-center my-custom-row">
            <div class="col-lg-1">
                <i class="fas fa-lock fa-4x my-custom-icon"></i>
            </div>
            <div class="col-lg-11">
                <h2 class="title my-custom-title">{{ __('Edit Password ') }}</h2>
                <span class="lead my-custom-lead">{{ __('Update your password regularly to help secure your data.') }}</span>
            </div>
        </div>                
    </div>
    <div class="col-lg-12">
        <div class="scard bg-white">
            <form class="form" action="{{ route('account.password.update') }}" method="post" role="form">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="email">{{ __('Email') }}</label>
                        <input type="text" class="form-control disabled" value="{{ Auth()->user()->email }}" name="email" id="email" disabled>
                    </div>
                    <div class="form-group">
                        <label for="old_password">{{ __('Current Password') }}</label>
                        <input type="password" class="form-control" name="old_password" id="old_password" value="">
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('New Password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" value="">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="">
                    </div>
                </div>
                <div class="card-footer">
                    <div class="progress">
                        <div id="pstrength" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div style="text-align: right; padding: 20px;">
                        <button type="submit" class="btn btn-primary pull-right">
                            {{ __('Update account') }}
                            <i class="fa fa-arrow-circle-right"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        function passwordStrength(password) {
            var desc = [
                { 'width': '0px' },
                { 'width': '20%' },
                { 'width': '40%' },
                { 'width': '60%' },
                { 'width': '80%' },
                { 'width': '100%' }
            ];

            var descClass = [
                '',
                'bg-danger',
                'bg-danger',
                'bg-warning',
                'bg-success',
                'bg-success'
            ];

            var score = 0;

            if (password.length > 8) score++;
            if ((password.match(/[a-z]/)) && (password.match(/[A-Z]/))) score++;
            if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;
            if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;
            if (password.length > 8) score++;

            $("#pstrength").removeClass(descClass[score - 1])
                .addClass(descClass[score])
                .css(desc[score]);
        }

        $(document).ready(function() {
            $("input#old_password").focus();
            $("input#password").keyup(function() {
                passwordStrength($(this).val());
            });

            // Show/hide password
            $("#togglePassword").click(function() {
                var passwordInput = $("#password");
                var passwordType = passwordInput.attr("type");

                if (passwordType === "password") {
                    passwordInput.attr("type", "text");
                    $(this).html('<i class="fas fa-eye-slash"></i>');
                } else {
                    passwordInput.attr("type", "password");
                    $(this).html('<i class="fas fa-eye"></i>');
                }
            });
        });
    </script>
@endsection
