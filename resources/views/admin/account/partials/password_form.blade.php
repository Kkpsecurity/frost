<div class="row">
    <style>
        .numberCircle {
            border-radius: 50%;
            /* remove if you don't care about IE8 */
            width: 25px;
            line-height: 23px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            font-size: 12px;
            border: 2px solid #0aa89e;
            float: left;
            margin-right: 20px
        }
    </style>
    <div class="col-lg-12">
        <h4 class="title ">{{ __('Reset Password') }}</h4>
        <div class="lead">
           Here you can reset your password,
        </div>
    </div>
    <div class="col-lg-8 ">
        <div class="pw-message_console"></div>
        <div class="card">
            <div class="card-head style-default p-3">
                <header><i class="fa fa-lock"></i> Manage Password</header>
            </div>
            <form  action="{{ route('admin.account.password.update') }}" class="form password-form needs-validation" method="post" role="form">
                @csrf
            <div class="card-body bg-dark">

                    <div class="form-group">
                        <label for="email">{{__('Email')}}</label>
                        <input type="text" class="form-control" disabled value="{{Auth()->user()->email}}" name="email" id="email">
                    </div>

                    <div class="form-group">
                        <label for="old_password">{{__('Current Password')}}</label>
                        <input type="password" class="form-control" name="old_password" id="old_password">
                    </div>
                    <div class="form-group">
                        <label for="password">{{__('Password')}}</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{__('Confirm Password')}}</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                    </div>
            </div>
            <div class="card-actionbar bg-dark p-3">
                <div class="progress progress-striped active">
                    <div id="jak_pstrength" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                </div>
                <div class="card-actionbar-row p-3 clearfix">
                    <button type="submit" class="btn btn-flat btn-primary ink-reaction float-right submit-password-btn">
                        {{__('Update account')}}
                        <i class="fa fa-arrow-circle-right"></i>
                    </button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4 bg-info p-2">
        <article class="">
            <h4 class="p-2 m-0">{{ __('Password Security.') }}</h4>
            <p class="lead p-2">Here are six tips for ensuring your passwords are as strong as possible.</p>
            <ul class="list-group divider-full-bleed bg-light m-b-20" style="border-radius: 20px;">
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">1</div>
                        </div>
                        <div class="tile-text" >
                            Make Your Password Long at lease 8 characters. ...
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">2</div>
                        </div>
                        <div class="text-xs" >
                            Make Your Password a Non-Sense Phrase. ...
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">3</div>
                        </div>
                        <div class="tile-text" >
                            Include Numbers, Symbols, and Uppercase and Lowercase Letters. ...
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">4</div>
                        </div>
                        <div class="tile-text" >
                            Avoid Using Obvious Personal Information. ...
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">5</div>
                        </div>
                        <div class="tile-text" >
                            Do Not Reuse Passwords. ...
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a class="tile-content ink-reaction">
                        <div class="tile-icon">
                            <div class="numberCircle">6</div>
                        </div>
                        <div class="tile-text" >
                            Start Using a Password Manager. ...
                        </div>
                    </a>
                </li>
            </ul>
        </article>
    </div>
</div>
