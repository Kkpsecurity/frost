@extends('adminlte::page')

@section('title', 'Admin Users')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Admin Users</h1>
        <a href="{{ route('admin.admin-center.admin-users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Admin User
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Admin Users</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="admin-users-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#admin-users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.admin-center.admin-users.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });

        function deleteAdmin(adminId) {
            if (confirm('Are you sure you want to delete this admin user? This action cannot be undone.')) {
                $.ajax({
                    url: '/admin/admin-center/admin-users/' + adminId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#admin-users-table').DataTable().ajax.reload();
                            alert('Admin user deleted successfully.');
                        } else {
                            alert('Error: ' + (response.message || 'Failed to delete admin user.'));
                        }
                    },
                    error: function(xhr) {
                        alert('Error: Failed to delete admin user.');
                        console.error(xhr.responseText);
                    }
                });
            }
        }
    </script>
@stop
