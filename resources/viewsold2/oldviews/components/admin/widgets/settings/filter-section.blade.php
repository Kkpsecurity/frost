<div class="px-3 py-3 border-bottom admin-dark-filter filter-section" id="filter-section" style="display: none;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label for="group-filter" class="admin-dark-text-muted small">Filter by Group:</label>
                <select id="group-filter" class="form-control form-control-sm">
                    <option value="">All Settings Groups</option>
                    <option value="general">General Settings</option>
                    @if (!empty($groups))
                        @foreach ($groups as $prefix)
                            @if ($prefix !== 'general')
                                <option value="{{ $prefix }}">{{ ucfirst($prefix) }} Settings</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <small class="admin-dark-text-muted">
                <i class="fas fa-info-circle"></i>
                Manage application settings and configuration
            </small>
        </div>
    </div>
</div>
