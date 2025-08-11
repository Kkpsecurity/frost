@extends('adminlte::page')

@section('title', 'Student Management')

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <x-admin.datatables.students.student-datatable />
@stop

@section('css')
    @vite('resources/css/admin.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        let studentsTable;

        $(document).ready(function() {
            initializeStudentsTable();
        });

        function initializeStudentsTable() {
            studentsTable = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.students.data") }}',
                    data: function (d) {
                        d.account_status_filter = $('#account_status_filter').val();
                        d.email_verified_filter = $('#email_verified_filter').val();
                        d.registration_date_filter = $('#registration_date_filter').val();
                    }
                },
                columns: [
                    {data: 'avatar_display', name: 'avatar', orderable: false, searchable: false},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'is_active'},
                    {data: 'email_verified_status', name: 'email_verified_at'},
                    {data: 'last_login_formatted', name: 'last_login'},
                    {data: 'formatted_created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']],
                pageLength: 25
            });

            // Filter functionality
            $('#account_status_filter, #email_verified_filter, #registration_date_filter').change(function() {
                studentsTable.draw();
            });

            $('#clear_account_filters').click(function() {
                $('#account_status_filter, #email_verified_filter, #registration_date_filter').val('').trigger('change');
            });

            // Toggle filter section
            $('#toggle-filters').click(function() {
                $('#filter-section').toggle();
            });
        }

        function toggleFilters() {
            const filterSection = $('#filter-section');
            const filterBtn = $('#filter-btn-text');

            if (filterSection.is(':visible')) {
                filterSection.hide();
                filterBtn.text('Show Filters');
            } else {
                filterSection.show();
                filterBtn.text('Hide Filters');
            }
        }

        function addStudent() {
            window.location.href = '{{ route("admin.students.create") }}';
        }

        function viewStudent(id) {
            $.ajax({
                url: `/admin/students/${id}`,
                method: 'GET',
                success: function(data) {
                    $('#studentDetailsContent').html(data);
                    $('#studentDetailsModal').modal('show');
                },
                error: function() {
                    alert('Failed to load student details');
                }
            });
        }

        function editStudent(id) {
            window.location.href = `/admin/students/${id}/edit`;
        }

        function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student?')) {
                $.ajax({
                    url: `/admin/students/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        studentsTable.ajax.reload();
                        alert('Student deleted successfully');
                    },
                    error: function() {
                        alert('Failed to delete student');
                    }
                });
            }
        }
    </script>
@stop
