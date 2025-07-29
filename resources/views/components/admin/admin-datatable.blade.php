<div class="row">
    <div class="col-12">
        <div class="card mt-3 admin-dark-card">
            <x-admin.admin-table-crud-bar />
            <div class="card-body p-0">
                <!-- Role Filter Section -->
                <div class="px-3 py-3 border-bottom admin-dark-filter filter-section visible" id="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <x-admin.role-filter />
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="admin-dark-text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing admin-level users (System Admin, Admin, Instructors, Support)
                            </small>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="admin-users-table" class="table table-bordered table-striped table-hover mb-0  table-dark admin-dark-table"
                        style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
