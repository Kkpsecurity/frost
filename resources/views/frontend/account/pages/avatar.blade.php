<div class="row avatar-view">
    <div class="col-lg-12">
        <div class="row align-items-center my-custom-row">
            <div class="col-lg-1">
                <i class="fas fa-upload fa-3x my-custom-icon"></i>
            </div>
            <div class="col-lg-11">
                <h2 class="title my-custom-title">{{ __('Change Avatar') }}</h2>
                <span
                    class="fs-5 fw-normal">{{ __('Manage Your Avatar. Include Your Gravatar or upload your Own Photo.') }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 d-none d-lg-block">
        <div class="p-4">
            <div class="text-center">
                <img class="rounded-circle border border-5 img-fluid" src="{{ $user->getAvatar('regular') }}"
                    style="width: 240px" alt="{{ $user->username }}" />
                <h3 class="h4">
                    <small>{{ $user->fname }} {{ $user->lname }}</small>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="value-props p-3">
            @if (Auth()->user()->avatar == '')
                <h3 class="title">{{ __('UPLOAD AVATAR') }}</h3>
            @else
                <h3 class="title">{{ __('DELETE AVATAR') }}</h3>
            @endif
            <hr>
            <p class="text-black fs-6" style="line-height: 20px;">
                <i>{{ __('Note: Registered Email must match that of Registered Gravatar Email') }}</i>
            </p>
            <form action="{{ route('account.update.gravatar') }}" class="form" id="gravatar-form" method="post"
                enctype="multipart/form-data">
                @csrf

                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-lg-11 d-flex align-items-center text-white">
                            <strong>{{ __('Active Gravatar') }}</strong> -
                            <a href="https://gravatar.com" target="_blank">Learn more</a>
                        </div>
                        <div class="col-lg-1 text-end">
                            <div class="form-check form-switch text-end">
                                <input class="form-check-input" type="checkbox" name="use_gravatar" id="use_gravatar"
                                    {{ Auth()->user()->use_gravatar === false ? '' : 'checked' }}>
                                <label class="form-check-label" for="use_gravatar"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form action="{{ route('account.avatar.upload') }}" class="form" id="avatar-form" method="post"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-lg-12">
                        @if (Auth()->user()->avatar == '')
                            <div class="d-flex align-items-center justify-content-center p-5"
                                style="width: 100%; background: #ccc; cursor: {{ Auth()->user()->use_gravatar === false ? 'crosshair' : 'pointer' }}"
                                class="content">

                                <div id="preview-container" style="display: none">
                                    <img id="preview" src="#" alt="your image" />
                                    <button type="button" id="resetButton" class="btn btn-warning float-end mt-2">
                                        <i class="fa fa-times"></i> Reset
                                    </button>
                                </div>

                                <label for="avatar" class="upload-label"
                                    style="cursor: {{ Auth()->user()->use_gravatar === false ? 'disabled' : 'disabled ' }}; width: 100%; text-align: center ">
                                    <i class="fas fa-cloud-upload-alt fa-5x"></i><br>
                                    <span>{{ __('Drag and drop or click to upload') }}</span>
                                    <input type="file" id="avatar" name="avatar"
                                        class="file-input visually-hidden">
                                </label>

                            </div>
                            <hr>
                            <button type="submit" id="uploadButton" class="btn btn-success float-end mt-2"
                                {{ Auth()->user()->use_gravatar === false ? '' : 'disabled' }}>
                                <i class="fa fa-upload"></i> {{ __('Upload') }}
                            </button>
                        @else
                            <div class="mb-3">
                                <a href="{{ route('account.avatar.delete') }}" class="btn btn-danger btn-block">
                                    <i class="fa fa-trash-alt"></i> {{ __('Remove Avatar') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const uploadLabel = document.querySelector('.upload-label');
                        const fileInput = document.querySelector('.file-input');
                        const gravatarSwitch = document.querySelector('#use_gravatar');
                        const uploadButton = document.querySelector('#uploadButton');
                        const resetButton = document.querySelector('#resetButton');

                        // Handle click on the upload label
                        uploadLabel.addEventListener('click', function() {
                            if (!gravatarSwitch.checked) {
                                fileInput.click();
                            } else {
                                alert('You cannot upload an avatar while using Gravatar.');
                            }
                        });

                        fileInput.addEventListener('change', function(e) {
                            const reader = new FileReader();

                            reader.onload = function(event) {
                                const preview = document.getElementById('preview-container');
                                const img = document.getElementById('preview');
                                img.src = event.target.result;
                                img.style.display = 'block';
                                preview.style.display = 'block';
                                uploadLabel.style.display = 'none';
                                resetButton.style.display = 'block'; // Show reset button when an image is uploaded
                            }

                            reader.readAsDataURL(e.target.files[0]);
                        });

                        resetButton.addEventListener('click', function() {
                            const preview = document.getElementById('preview-container');
                            fileInput.value = null;
                            const img = document.getElementById('preview');
                            img.src = '#';
                            img.style.display = 'none';
                            preview.style.display = 'block';
                            uploadLabel.style.display = 'block'; // Show upload label when image is reset
                            resetButton.style.display = 'none'; // Hide reset button when clicked
                        });

                        // Handle change of the gravatar switch
                        gravatarSwitch.addEventListener('change', function() {
                            document.querySelector('#gravatar-form').submit();
                            uploadButton.disabled = this.checked;
                            fileInput.disabled = this.checked;
                        });

                        // Disable the upload button and file input if the gravatar switch is checked
                        uploadButton.disabled = gravatarSwitch.checked;
                        fileInput.disabled = gravatarSwitch.checked;
                    });
                </script>

            </form>
        </div>
    </div>
</div>
