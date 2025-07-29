<div class="card-header bg-dark" style="margin: 0 !important; padding: 15px 15px !important;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="card-title text-white mb-0">
                <i class="fas fa-users mr-2"></i>Admin Users Management
            </h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="#" class="btn btn-primary btn-sm" id="toggle-filters-btn" onclick="toggleFilters()">
                <i class="fas fa-filter mr-1"></i><span id="filter-btn-text">Show Filters</span>
            </a>
            <a href="{{ route('admin.admin-center.admin-users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add New Admin
            </a>
        </div>
    </div>
</div>
