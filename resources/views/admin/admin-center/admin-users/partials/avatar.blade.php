<form action="{{ route('admin.admin-center.adminusers.update', [$user->id, $tab_id]) }}" enctype="multipart/form-data" method="POST" id="{{ $tab_id }}-form">
    @csrf

    <div class="row">
        <div class="col-lg-12">
            @include('admin.partials.admin-messages')
            <h2>{{ __('Upload Avatar') }}</h2>
            <span>{{ __('Manage Your Avatar. Include Your Gravatar or upload your Own Photo.') }}</span>
        </div>

        <div class="col-lg-6">
            <div class="value_props">
                <div class="value_content p-3">
                    <center>
                        <img class="img-circle border-white border-xl img-responsive auto-width"
                             src="{{ $user->getAvatar('regular') }}"
                             style="width: 240px" alt="{{ $user->username }}" />
                        <h3>
                            {{ $user->email }}<br/>
                            <small>{{ $user->fname }} {{ $user->lname }}</small>
                        </h3>
                    </center>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="value_props" style="clear: both;">
                    @if($user->avatar == '')
                        <h2 class="title">{{ __('UPLOAD AVATAR') }}</h2>
                    @else
                        <h2 class="title">{{ __('DELETE AVATAR') }}</h2>
                    @endif
                    <hr>
                        <div class="row">

                            <div class="col-lg-7">
                            <span style="font-size: 18px;">
                                {{ __('Active Gravatar') }} - <a href="https:\\gravatar.com" target="_blank">Learn more</a>
                            </span>
                                <p class="text-black" style="font-size: 12px; line-height: 20px;">
                                    <i>{{ __('Note: Registered Email must match that of Registered Gravatar Email') }}</i>
                                </p>
                            </div>
                            <div class="col-lg-5">
                                <div class="ui toggle checkbox _1457s2">
                                    <label></label>
                                    <input type="checkbox" name="use_gravatar" {{ ($user->use_gravatar == "" ? "" : 'checked') }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <i class="fa fa-upload fa-5x" style="margin-top: 20px; font-size: 50px;"></i>
                            </div>
                            <div class="col-lg-8">
                                @if($user->avatar == '')
                                    <div class="part_input mt-30 lbel25">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="bg-dark custom-file-input" name="avatar" id="avatar">
                                                <label class="custom-file-label" for="avatar">{{ __('Choose Avatar') }} - (jpg, jpeg, png, gif)</label>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="part_input mt-30 lbel25">
                                        <div class="input-group">
                                            <div class="custom-file center aligned">
                                                <a href="{{ route('admin.admin-center.adminusers.delete', [$user->id, $tab_id]) }}" class="btn btn-danger btn-block">
                                                    <i class="fa fa-trash"></i> {{ __('Remove Avatar') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-success pull-right" style="margin: 20px; float: right;"><i class="fa fa-upload"></i> {{ __('Upload') }}</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</form>
