@extends('adminlte::page')

@section('title', 'Admin Users')

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <x-admin.datatables.admin-datatable />
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
        $(document).ready(function() {
            var table = $('#admin-users-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.admin-center.admin-users.data') }}',
                    data: function(d) {
                        d.role_filter = $('#role-filter').val();
                    }
                },
                columns: [{
                        data: 'avatar_display',
                        name: 'avatar_display',
                        orderable: false,
                        searchable: false,
                        width: '50px'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role_badge',
                        name: 'Role.name',
                        width: '120px'
                    },
                    {
                        data: 'status',
                        name: 'is_active',
                        width: '80px'
                    },
                    {
                        data: 'formatted_created_at',
                        name: 'created_at',
                        width: '170px'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '120px'
                    }
                ],
                order: [
                    [5, 'desc']
                ],
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

        function toggleFilters() {
            const filterSection = document.getElementById('filter-section');
            const filterBtn = document.getElementById('filter-btn-text');
            const filterIcon = document.querySelector('#toggle-filters-btn i');

            if (filterSection.classList.contains('hidden')) {
                filterSection.classList.remove('hidden');
                filterSection.classList.add('visible');
                filterBtn.textContent = 'Hide Filters';
                filterIcon.className = 'fas fa-filter mr-1';
            } else {
                filterSection.classList.remove('visible');
                filterSection.classList.add('hidden');
                filterBtn.textContent = 'Show Filters';
                filterIcon.className = 'fas fa-filter-slash mr-1';
            }
        }

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
