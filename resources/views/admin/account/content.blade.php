<div class="col-md-9">
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#account" data-toggle="tab">Account</a></li>
                <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Password</a></li>
                <li class="nav-item"><a class="nav-link" href="#avatar" data-toggle="tab">Avatar</a></li>
                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">


                <div class="active tab-pane fade active  show" id="account">
                    @include('admin.account.partials.profile_form')
                </div>

                <div class="tab-pane fade" id="password">
                    @include('admin.account.partials.password_form')
                </div>

                <div class="tab-pane fade" id="avatar">
                    @include('admin.account.partials.avatar_form')
                </div>

                <div class="tab-pane fade" id="settings">
                    <h2>Settings</h2>
                </div>

            </div>


        </div>
    </div>
</div>
