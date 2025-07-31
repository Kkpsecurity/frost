@extends('adminlte::page')

@section('title', 'Settings Management')

@section('content_header')
    <x-admin.admin-header />
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Settings Management Card -->
                <div class="card mt-3 admin-dark-card">
                    <x-admin.widgets.settings.general-header :title="'Settings Management'" />

                    <div class="card-body p-0">
                        <x-admin.widgets.messages :message="session('success')" type="success" />

                        <!-- Filter Section -->
                        @php
                            $groups = count($groupedSettings) > 0 ? array_keys($groupedSettings) : [];
                        @endphp
                        <x-admin.widgets.settings.filter-section :groups="$groups" />

                        @if (count($groupedSettings) > 0)
                            <x-admin.widgets.settings.admin-general-setting-datatable :settings="$groupedSettings" />
                        @else
                            <div class="p-4 text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <h5>No settings found</h5>
                                    <p class="mb-3">Get started by creating your first setting.</p>
                                    <a href="{{ route('admin.settings.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create First Setting
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/admin.css')
    @vite('resources/css/admin-settings.css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@stop

@section('js')
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip({
                html: true,
                placement: 'top'
            });

            // Initialize Settings DataTable - Similar to Admin Users
            var table = $('#settingsTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "scrollX": true,
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "order": [[0, "asc"]],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                       '<"row"<"col-sm-12"tr>>' +
                       '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "processing": '<i class="fas fa-spinner fa-spin"></i> Loading...',
                    "search": "Search settings:",
                    "lengthMenu": "Show _MENU_ settings per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ settings",
                    "infoEmpty": "No settings available",
                    "infoFiltered": "(filtered from _MAX_ total settings)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "emptyTable": "No settings found"
                },
                "columnDefs": [
                    {
                        "targets": 5, // Actions column
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    },
                    {
                        "targets": 4, // Status column
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    }
                ],
                "drawCallback": function() {
                    // Ensure table takes full width
                    $('#settingsTable').css('width', '100%');

                    // Reinitialize tooltips for new content
                    $('[data-toggle="tooltip"]').tooltip({
                        html: true,
                        placement: 'top'
                    });
                },
                "initComplete": function(settings, json) {
                    // Add custom styling after table initialization
                    $('.dataTables_filter input').attr('placeholder', 'Type to search settings...');

                    // Add search info
                    $('.dataTables_filter').append(
                        '<small class="form-text text-muted">Search by key, value, type, or group</small>'
                    );
                }
            });

            // Handle group filter change - Similar to role filter in admin users
            $('#group-filter').on('change', function() {
                var selectedGroup = $(this).val();

                if (selectedGroup) {
                    // Filter table by data-group attribute
                    table.column(3).search(selectedGroup).draw();
                } else {
                    // Clear filter
                    table.column(3).search('').draw();
                }
            });

            // Auto-hide alerts after 5 seconds
            $('.alert').delay(5000).fadeOut(300);
        });

        // Toggle filters function - Match admin users functionality
        function toggleFilters() {
            const filterSection = document.getElementById('filter-section');
            const button = event.target.closest('button');
            const icon = button.querySelector('i');

            if (filterSection.style.display === 'none' || filterSection.style.display === '') {
                filterSection.style.display = 'block';
                button.innerHTML = '<i class="fas fa-filter"></i> Hide Filters';
                icon.classList.remove('fa-filter');
                icon.classList.add('fa-eye-slash');
            } else {
                filterSection.style.display = 'none';
                button.innerHTML = '<i class="fas fa-filter"></i> Show Filters';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-filter');
            }
        }

        // Enhanced delete confirmation
        function confirmDelete(settingKey) {
            return confirm(
                'Are you sure you want to delete the setting "' + settingKey + '"?\n\n' +
                'This action cannot be undone and may affect application functionality.'
            );
        }
    </script>
@stop
