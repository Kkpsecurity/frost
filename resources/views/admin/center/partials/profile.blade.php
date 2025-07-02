<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

{{ Form::open(['url' => url('admin/center/user/update/' . $user->id . '/' . $tab_id)]) }}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="fname">{{ __('First Name') }}</label>
                    {{ Form::text('fname', old('fname', $user->fname), ['class' => 'form-control input-solid', 'placeholder' => __('First Name'), 'id' => 'fname']) }}
                </div>

                <div class="form-group">
                    <label for="lname">{{ __('Last Name') }}</label>
                    {{ Form::text('lname', old('lname', $user->lname), ['class' => 'form-control input-solid', 'placeholder' => __('Last Name'), 'id' => 'lname']) }}
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    {{ Form::email('email', old('email', $user->email), ['class' => 'form-control input-solid', 'placeholder' => __('Email'), 'id' => 'email']) }}
                </div>

                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <div style="text-align: left; color: #222; font-size: 14px;">{{ __('Course Visibility') }}</div>
                        <div class="switch">
                            {{ Form::hidden('is_active', '0') }}
                            {{ Form::checkbox('is_active', '1', old('is_active', $user->is_active), ['class' => 'switch', 'id' => 'is_active']) }}
                            <label for="is_active"></label>
                        </div>
                        <div class="ui toggle" style="padding: 10px; float: left;">
                            <label class="switch">
                                {{ Form::checkbox('is_active', '1', old('is_active', $user->is_active), ['style' => 'margin-left: 20px;']) }}
                                <span class="slider round"></span>
                            </label>
                            <span style="color: #111; padding: 10px; font-size: 14px;">{{ __('Set User Status') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role_id">{{ __('Role') }}</label>
                    {!! Form::select(
                        'role_id',
                        [
                            '0' => __('Select an Option'),
                            '1' => __('Sys Admin'),
                            '2' => __('Administrator'),
                            '3' => __('Instructor'),
                            '4' => __('Student'),
                        ],
                        old('role_id', $user->role_id),
                        ['class' => 'form-control input-solid', 'id' => 'role_id'],
                    ) !!}
                </div>

                <div class="form-group">
                    <label for="seclvl">{{ __('Student Security Level') }}</label>
                    {!! Form::select(
                        'seclvl',
                        [
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                        ],
                        old('seclvl', $user->seclvl),
                        ['class' => 'form-control input-solid', 'id' => 'seclvl'],
                    ) !!}
                </div>

                <div class="form-group">
                    {{ Form::button('<i class="fa fa-save"></i> ' . __('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}
