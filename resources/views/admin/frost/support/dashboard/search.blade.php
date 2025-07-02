<div class="card card-primary card-outline bg-dark">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin.frost-support.dashboard.search') }}" class="form" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="qsearch" class="form-control float-right" placeholder="Search Student by name/email">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
