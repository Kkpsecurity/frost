<div class="card-header bg-list">
    <div class="row">
        <div class="col-lg-6 d-flex justify-content-center align-items-center">
            {{ Form::open([
                'url' => route('admin.center.adminusers'),
                'method' => 'post',
                'class' => 'form',
                'id' => 'status-form',
            ]) }}
            <div class="form-group">
                {{ Form::select(
                    'type_id',
                    ['' => 'Select a User Role'] +
                        array_values(
                            array_filter(\App\Support\Enum\UserRoles::options(), function ($value) {
                                return $value !== 'Student';
                            }),
                        ),
                    request('type_id'),
                    ['class' => 'form-control form-control-lg', 'id' => 'type_id'],
                ) }}
            </div>
            {{ Form::close() }}
        </div>

        <div class="col-lg-6 d-flex justify-content-center align-items-center">
            {{ Form::open([
                'url' => route('admin.center.adminusers'),
                'method' => 'post',
                'class' => 'form',
            ]) }}
            <div class="input-group mb-3">
                <div class="form-group">
                    <div class="input-group input-group-lg">
                        {{ Form::text('search', request('search'), ['class' => 'form-control form-control-lg', 'id' => 'search', 'placeholder' => 'Type your keywords here']) }}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-lg btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
