<div class="row">
    <div class="col-md-3">
        <div class="form-group mb-0">
            <label for="account_status_filter" class="form-label mb-1">
                <i class="fas fa-filter"></i> Account Status:
            </label>
            <select id="account_status_filter" class="form-control form-control-sm">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-0">
            <label for="email_verified_filter" class="form-label mb-1">
                <i class="fas fa-envelope"></i> Email Status:
            </label>
            <select id="email_verified_filter" class="form-control form-control-sm">
                <option value="">All Email Status</option>
                <option value="verified">Verified</option>
                <option value="unverified">Unverified</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-0">
            <label for="registration_date_filter" class="form-label mb-1">
                <i class="fas fa-calendar"></i> Registration Date:
            </label>
            <input type="date" id="registration_date_filter" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-0">
            <label class="form-label mb-1">&nbsp;</label>
            <button id="clear_account_filters" class="btn btn-secondary btn-sm btn-block">
                <i class="fas fa-times"></i> Clear Filters
            </button>
        </div>
    </div>
</div>
