<div class="row">
    <div class="col-lg-12">
        <h4 class="title">{{ __('Basic Profile Information') }}</h4>
        <div class="lead">
            {{ __('In this section you can update basic information related to your account.') }}
        </div>
    </div>

    <div class="col-sm-12">
        <div class="message_console"></div>
        <form class="form account-form needs-validation" action="{{ route('admin.account.update') }}" method="post" role="form">
            @csrf
            <div class="card">
                <div class="card-head style-default p-3">
                    <header><i class="fa fa-user"></i> {{ __('Manage Account') }}</header>
                </div>

                <div class="card-body bg-dark">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="first_name">{{__('First Name')}}</label>
                                <input type="text" class="form-control" value="{{Auth()->user()->fname}}" name="fname" id="fname" required>
                                <div class="invalid-feedback">
                                    {{ __('Please provide a valid first name.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="last_name">{{__('Last Name')}}</label>
                                <input type="text" value="{{Auth()->user()->lname}}" class="form-control" name="lname" id="lname" required>
                                <div class="invalid-feedback">
                                    {{ __('Please provide a valid last name.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="email">{{__('Email')}}</label>
                                <input type="email" value="{{Auth()->user()->email}}" class="form-control" name="email" id="email" required>
                                <div class="invalid-feedback">
                                    {{ __('Please provide a valid email.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <div class="input-group date" id="dob">
                                    <input type="date" class="form-control" id="don" name="dob" value="{{ Auth()->user()->dob }}">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /body -->

                <div class="card-actionbar bg-dark p-3">
                    <div class="card-actionbar-row">
                        <button type="submit" class="btn btn-flat btn-primary ink-reaction float-right update-account-btn">
                            {{__('Update Account')}}
                            <i class="fa fa-arrow-circle-right"></i>
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

