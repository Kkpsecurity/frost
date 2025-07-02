<div class="row">
    <div class="row" style="padding: 20px !important;">
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
            <h4>Change Password</h4>
        </div>
        <!--end .col -->
        <div class="col-lg-4 col-md-4">
            <article class="margin-bottom-xxl">
                <h4>{{ __('In this section you can update the account password.') }}</h4>

                <p style="font-size: 12px;">Here are six tips for ensuring the password is as strong as possible.</p>
                <ul class="list divider-full-bleed" style="background: #fff;">
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">1</div>
                            </div>
                            <div class="tile-text" style="font-size: 12px;">
                                Make Your Password Long at lease 8 characters. ...
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">2</div>
                            </div>
                            <div class="text-xs" style="font-size: 12px;">
                                Make Your Password a Non-Sense Phrase. ...
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">3</div>
                            </div>
                            <div class="tile-text" style="font-size: 12px;">
                                Include Numbers, Symbols, and Uppercase and Lowercase Letters. ...
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">4</div>
                            </div>
                            <div class="tile-text" style="font-size: 12px;">
                                Avoid Using Obvious Personal Information. ...
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">5</div>
                            </div>
                            <div class="tile-text" style="font-size: 12px;">
                                Do Not Reuse Passwords. ...
                            </div>
                        </a>
                    </li>
                    <li class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                                <div class="numberCircle">6</div>
                            </div>
                            <div class="tile-text" style="font-size: 12px;">
                                Start Using a Password Manager. ...
                            </div>
                        </a>
                    </li>
                </ul>
            </article>
        </div>
        <div class="col-lg-offset-1 col-md-6 col-sm-6">

            {{ Form::open(['route' => ['admin.center.adminusers.update', $user->id, $tab_id], 'class' => 'form', 'role' => 'form']) }}
            @csrf
            <div class="card">
                <div class="card-head style-default">
                    <header><i class="fa fa-lock"></i> Manage Password</header>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('email', __('Email')) }}
                        {{ Form::text('email', $user->email, ['class' => 'form-control', 'disabled' => true, 'id' => 'email']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('old_password', __('Current Password')) }}
                        {{ Form::password('old_password', ['class' => 'form-control', 'id' => 'old_password']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('password', __('Password')) }}
                        {{ Form::password('password', ['class' => 'form-control', 'id' => 'password']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('password_confirmation', __('Confirm Password')) }}
                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'password_confirmation']) }}
                    </div>
                </div>
                <div class="card-actionbar">
                    <div class="progress progress-striped active">
                        <div id="jak_pstrength" class="progress-bar" role="progressbar" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                    </div>
                    <div class="card-actionbar-row">
                        {{ Form::button(__('Update account') . ' <i class="fa fa-arrow-circle-right"></i>', ['type' => 'submit', 'class' => 'btn btn-flat btn-primary ink-reaction']) }}
                    </div>
                </div>
            </div>
            {{ Form::close() }}


        </div>
    </div>
</div>
