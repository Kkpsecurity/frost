@extends('adminlte::page')

@section('title', 'Student Management Dashboard')

@section('content_header')
    <x-admin.partials.titlebar
        title="Student Management"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Students']
        ]"
    />
@endsection

@section('content')
    <div class="row">
        {{-- Student Statistics Cards --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-students">-</h3>
                    <p>Total Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="active-students">-</h3>
                    <p>Active Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="new-this-month">-</h3>
                    <p>New This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="total-orders">-</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Student Search and Management --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="exportStudents()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Search and Filter Controls --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" id="student-search" class="form-control" placeholder="Search students by name, email, or phone...">
                        </div>
                        <div class="col-md-3">
                            <select id="status-filter" class="form-control">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary" onclick="searchStudents()">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>

                    {{-- Students Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped" id="students-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Orders</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="students-tbody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Loading students...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <nav id="students-pagination"></nav>

                    {{-- Bulk Actions --}}
                    <div class="mt-3" id="bulk-actions" style="display: none;">
                        <strong>Bulk Actions:</strong>
                        <button type="button" class="btn btn-success btn-sm ml-2" onclick="bulkActivate()">
                            <i class="fas fa-check"></i> Activate Selected
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                            <i class="fas fa-times"></i> Deactivate Selected
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="bulkEmail()">
                            <i class="fas fa-envelope"></i> Email Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    loadStats();
    loadStudents();
});

function loadStats() {
    $.get('/admin/students/data/stats/overview')
        .done(function(response) {
            if (response.status === 'success') {
                $('#total-students').text(response.data.total_students);
                $('#active-students').text(response.data.active_students);
                $('#new-this-month').text(response.data.new_this_month);
                $('#total-orders').text(response.data.total_orders);
            }
        })
        .fail(function() {
            console.error('Failed to load stats');
        });
}

function loadStudents(page = 1) {
    const search = $('#student-search').val();
    const status = $('#status-filter').val();

    $.get('/admin/students/data/students/list', {
        page: page,
        search: search,
        status: status
    })
    .done(function(response) {
        if (response.status === 'success') {
            renderStudentsTable(response.data);
        }
    })
    .fail(function() {
        $('#students-tbody').html('<tr><td colspan="7" class="text-center text-danger">Failed to load students</td></tr>');
    });
}

function renderStudentsTable(data) {
    let html = '';

    if (data.data.length === 0) {
        html = '<tr><td colspan="7" class="text-center">No students found</td></tr>';
    } else {
        data.data.forEach(function(student) {
            const statusBadge = student.status === 'active'
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';

            html += `
                <tr>
                    <td><input type="checkbox" class="student-checkbox" value="${student.id}"></td>
                    <td>${student.name}</td>
                    <td>${student.email}</td>
                    <td>${statusBadge}</td>
                    <td>${student.orders_count || 0}</td>
                    <td>${new Date(student.created_at).toLocaleDateString()}</td>
                    <td>
                        <a href="/admin/students/manage/${student.id}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/students/manage/${student.id}/edit" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }

    $('#students-tbody').html(html);

    // Update pagination
    renderPagination(data);

    // Update bulk actions visibility
    updateBulkActionsVisibility();
}

function renderPagination(data) {
    // Simple pagination implementation
    let html = '';

    if (data.last_page > 1) {
        html += '<ul class="pagination pagination-sm m-0 float-right">';

        for (let i = 1; i <= data.last_page; i++) {
            const active = i === data.current_page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadStudents(${i})">${i}</a></li>`;
        }

        html += '</ul>';
    }

    $('#students-pagination').html(html);
}

function searchStudents() {
    loadStudents(1);
}

function refreshData() {
    loadStats();
    loadStudents();
}

function exportStudents() {
    window.location.href = '/admin/students/bulk/export';
}

// Bulk actions
$('#select-all').change(function() {
    $('.student-checkbox').prop('checked', this.checked);
    updateBulkActionsVisibility();
});

$(document).on('change', '.student-checkbox', function() {
    updateBulkActionsVisibility();
});

function updateBulkActionsVisibility() {
    const checkedCount = $('.student-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulk-actions').show();
    } else {
        $('#bulk-actions').hide();
    }
}

function getSelectedStudentIds() {
    return $('.student-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function bulkActivate() {
    const studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) return;

    if (confirm(`Activate ${studentIds.length} selected students?`)) {
        $.post('/admin/students/bulk/activate', {
            student_ids: studentIds,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            alert(response.message);
            refreshData();
        })
        .fail(function() {
            alert('Failed to activate students');
        });
    }
}

function bulkDeactivate() {
    const studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) return;

    if (confirm(`Deactivate ${studentIds.length} selected students?`)) {
        $.post('/admin/students/bulk/deactivate', {
            student_ids: studentIds,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            alert(response.message);
            refreshData();
        })
        .fail(function() {
            alert('Failed to deactivate students');
        });
    }
}

function bulkEmail() {
    const studentIds = getSelectedStudentIds();
    if (studentIds.length === 0) return;

    // Simple prompt-based email (you can enhance this with a modal)
    const subject = prompt('Email Subject:');
    if (!subject) return;

    const message = prompt('Email Message:');
    if (!message) return;

    $.post('/admin/students/bulk/email', {
        student_ids: studentIds,
        subject: subject,
        message: message,
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        alert(response.message);
    })
    .fail(function() {
        alert('Failed to send bulk email');
    });
}

// Search on Enter key
$('#student-search').keypress(function(e) {
    if (e.which == 13) {
        searchStudents();
    }
});
</script>
@endsection
