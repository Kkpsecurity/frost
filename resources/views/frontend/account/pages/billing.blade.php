<div class="row billing-view">
    <div class="col-lg-12 ">
        <div class="row align-items-center my-custom-row">
            <div class="col-lg-1">
                <i class="fas fa-credit-card fa-3x my-custom-icon"></i>
            </div>
            <div class="col-lg-11">
                <h2 class="title my-custom-title">{{ __('Billing Information ') }}</h2>
                <span class="lead my-custom-lead">{{ __('Change your Passsword') }}</span>
            </div>
        </div> 
    </div>
    <div class="col-lg-12">
        <div class="scard bg-white">
            <form action="{{ route('account', ['billing', 'update']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ $user->billing_address }}">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ $user->billing_city }}">
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" value="{{ $user->billing_state }}">
                    </div>
                    <div class="form-group">
                        <label for="zip">ZIP Code</label>
                        <input type="text" class="form-control" id="zip" name="zip" value="{{ $user->billing_zip }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Billing Information</button>
                </div>
            </form>
        </div>
    </div>
</div>

