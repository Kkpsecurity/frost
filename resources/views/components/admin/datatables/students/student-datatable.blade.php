<div class="row">
    <div class="col-12">
        <div class="card mt-3 admin-dark-card">
            @include('components.admin.datatables.students.student-table-crud-bar')
            <div class="card-body p-0">
                <!-- Student Filter Section -->
                <div class="px-3 py-3 border-bottom admin-dark-filter filter-section" id="filter-section" style="display: none;">
                    @include('components.admin.widgets.student-filter')
                </div>
                <div class="table-responsive">
                    <table id="students-table" class="table table-bordered table-striped table-hover mb-0 table-dark admin-dark-table"
                        style="width: 100%;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Email Verified</th>
                                <th>Last Login</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Student Details Modal --}}
<div class="modal fade" id="studentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="studentDetailsContent">
                    {{-- Content will be loaded here via AJAX --}}
                </div>
            </div>
        </div>
    </div>
</div>
