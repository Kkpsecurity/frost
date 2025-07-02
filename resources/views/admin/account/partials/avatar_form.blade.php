<style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
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

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
<?php $enable_gravatar = false; ?>
<div class="row">
    <div class="col-lg-12">
        <h4 class="title">{{ __('Avatar Management') }}</h4>
        <div class="lead">
            {{ __('You can change your avatar or link it to your gravatar account.') }}
        </div>
    </div>
    <div class="col-lg-12">
        <div class="a-message_console"></div>
        <form action="{{ route('admin.account.avatar.upload') }}"
              class="form"
              id="avatar-form"
              method="post"
              enctype="multipart/form-data"
              role="form">
            @csrf
            <div class="card">
                <div class="card-head style-default p-3">
                    <header><i class="fa fa-image"></i> {{ __('Manage Avatar') }}</header>
                </div>
                <div class="card-body bg-gradient-dark">
                    <div class="row">
                        <div class="col-5">
                            <center>
                                <img class="img-circle border-white border-xl img-responsive auto-width"
                                     src="{{ Auth()->user()->getAvatar('regular') }}"
                                     alt="{{ Auth()->user()->username }}" />
                                <h3>
                                    {{ Auth()->user()->username }}<br/>
                                    {{ Auth()->user()->fname }} {{ Auth()->user()->lname }}
                                </h3>
                            </center>
                        </div>
                        <div class="col-7">
                            @if(Auth()->user()->avatar == '')
                                <h3 class="title">{{ __('MANAGE AVATAR') }}</h3>
                            @else
                                <h3 class="title">{{ __('DELETE AVATAR') }}</h3>
                            @endif

                            <div class="ui toggle">
                                <div class="row">
                                    @if($enable_gravatar)
                                        <div class="col-3 " style="padding: 5%">
                                            <center>
                                                <label class="switch">
                                                    <input class="" type="checkbox" name="use_gravatar" {{ (Auth()->user()->use_gravatar == "" ? "" : 'checked') }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </center>
                                        </div>
                                        <div class="col-9">
                                            <h3>{{ __('Activate Gravatar') }} - <a href="https:\\gravatar.com" class="text-white-50" style="font-size: 14px;" target="_blank">Learn more</a></h3>
                                            <p class="lead">
                                                <i>{{ __('Note: Registered Email must match that of Registered Gravatar Email') }}</i>
                                            </p>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <hr class="hr text-white-50">
                                        @if(Auth()->user()->avatar == '')
                                            <div class="container dz-message text-center">
                                               <div class="row">
                                                   <div class="col-2">
                                                       <center>
                                                            <i class="fa fa-upload fa-5x"></i>
                                                       </center>
                                                   </div>
                                                   <div class="col-10 text-left">
                                                       <h2 class="h4-title">{{ __('AVATAR UPLOAD') }}</h2>
                                                       <input type="file" id="avatar" name="avatar" class="form-upload">
                                                   </div>
                                               </div>
                                            </div>

                                            <button type="submit" class="btn btn-success float-right"><i class="md md-save"></i>  {{ __('Save') }}</button>
                                        @else
                                            <div class="mt-30">
                                                <div class="input-group">
                                                    <div class="custom-file center aligned">
                                                        <a href="{{ route('admin.account.avatar.delete') }}" class="btn btn-danger btn-block delete-avatar">
                                                            <i class="md md-delete"></i> {{ __('Remove Avatar') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <output id="output"></output>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
