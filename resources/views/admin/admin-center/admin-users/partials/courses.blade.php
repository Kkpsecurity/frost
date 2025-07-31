<div class="card">
    <div class="card-head">
        <h3 class="card-title" style="padding-left: 20px;"><i class="fa fa-graduation-cap"></i> Courses</h3>
    </div>
    <div class="card-body">
        <div id="user-message-console"></div>
        <table class="table table-bordered table-striped data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">{{ __('ID') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Purchase Date') }}</th>
                    <th>{{ __('Status') }}</th>

                    <th style="width: 140px; text-align: right;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>

                @dd($user)


            </tbody>
        </table>
    </div>
</div>
