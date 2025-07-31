<!-- Header Bar -->
<div class="card-header admin-dark-header">
    <h3 class="card-title mb-0">
        <i class="fas fa-cogs"></i> Settings Management
    </h3>
    <div class="card-tools d-flex align-items-center justify-content-end">
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleFilters()">
            <i class="fas fa-filter"></i> Show Filters
        </button>
        <a href="{{ route('admin.settings.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Add New Setting
        </a>
    </div>
</div>
