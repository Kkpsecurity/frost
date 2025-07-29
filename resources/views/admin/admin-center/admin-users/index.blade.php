@extends('adminlte::page')

@section('title', 'Admin Users')

@section('content_header')
    @include('admin.partials.impersonation-banner')

    <div class="row">
        <div class="col-sm-6">
            <h1>Admin Users</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Admin Center</li>
                <li class="breadcrumb-item active">Admin Users</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title text-white">Admin Users Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.admin-center.admin-users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Admin
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Role Filter Section -->
                    <div class="px-3 py-3 border-bottom bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <x-role-filter />
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Showing admin-level users (System Admin, Admin, Instructors, Support)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="admin-users-table" class="table table-bordered table-striped table-hover mb-0" style="width: 100%;">
                            <thead class="thead-light">
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
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        /* Ensure table takes full width */
        #admin-users-table {
            width: 100% !important;
        }

        /* Dark header styling */
        .card-header.bg-dark .card-title {
            color: #fff !important;
        }

        /* Table styling improvements */
        .table-responsive {
            overflow-x: auto;
        }

        /* DataTables wrapper styling */
        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 4px;
        }

        /* Avatar column styling */
        #admin-users-table td:first-child {
            text-align: center;
            vertical-align: middle;
        }

        /* Actions column styling */
        #admin-users-table td:last-child {
            text-align: center;
            vertical-align: middle;
        }

        /* Badge styling */
        .badge {
            font-size: 0.8em;
        }

        /* Role filter section styling */
        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        #role-filter {
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #role-filter:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Filter section responsive */
        @media (max-width: 767.98px) {
            .px-3.py-3.border-bottom .col-md-6.text-right {
                text-align: left !important;
                margin-top: 10px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#admin-users-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.admin-center.admin-users.data') }}',
                    data: function (d) {
                        d.role_filter = $('#role-filter').val();
                    }
                },
                columns: [
                    {data: 'avatar_display', name: 'avatar_display', orderable: false, searchable: false, width: '60px'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'email', name: 'email'},
                    {data: 'role_badge', name: 'Role.name', width: '120px'},
                    {data: 'status', name: 'is_active', width: '80px'},
                    {data: 'formatted_created_at', name: 'created_at', width: '150px'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false, width: '120px'}
                ],
                order: [[5, 'desc']],
                pageLength: 25,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    // Ensure table takes full width
                    $('#admin-users-table').css('width', '100%');
                }
            });

            // Handle role filter change
            $('#role-filter').on('change', function() {
                table.ajax.reload();
            });
        });

        function deleteAdmin(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.admin-center.admin-users.destroy', '') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#admin-users-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }
    </script>
@stop
