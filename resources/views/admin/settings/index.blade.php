@extends('adminlte::page')

@section('title', 'Site Settings')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Site Settings</h1>
        <a href="{{ route('admin.settings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Setting
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Site Settings</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <select class="form-control" id="group-filter">
                                <option value="">All Groups</option>
                                @foreach($groupedSettings as $group => $settings)
                                    <option value="{{ $group }}">{{ ucfirst($group) }} ({{ count($settings) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="settings-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Group</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settingsForTable as $setting)
                                    <tr data-group="{{ $setting['group'] ?? 'default' }}">
                                        <td>
                                            <span class="badge badge-info">{{ $setting['group'] ?? 'default' }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $setting['key'] }}</code>
                                        </td>
                                        <td>
                                            @php
                                                $value = $setting['value'];
                                                $isJson = is_string($value) && json_decode($value) !== null;
                                                $isBool = is_bool($value) || in_array(strtolower($value), ['true', 'false', '1', '0']);
                                            @endphp

                                            @if($isBool)
                                                <span class="badge badge-{{ (in_array(strtolower($value), ['true', '1']) || $value === true) ? 'success' : 'danger' }}">
                                                    {{ (in_array(strtolower($value), ['true', '1']) || $value === true) ? 'TRUE' : 'FALSE' }}
                                                </span>
                                            @elseif($isJson)
                                                <code class="text-muted">JSON</code>
                                                <small class="text-muted">({{ strlen($value) }} chars)</small>
                                            @else
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $value }}">
                                                    {{ strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isBool)
                                                <span class="badge badge-success">Boolean</span>
                                            @elseif($isJson)
                                                <span class="badge badge-info">JSON</span>
                                            @elseif(is_numeric($value))
                                                <span class="badge badge-warning">Number</span>
                                            @else
                                                <span class="badge badge-secondary">String</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.settings.show', $setting['key']) }}"
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.settings.edit', $setting['key']) }}"
                                                   class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteSetting('{{ $setting['key'] }}')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No settings found.
                                            <a href="{{ route('admin.settings.create') }}">Create your first setting</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Total settings: <strong>{{ count($settingsForTable) }}</strong>
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.settings.test') }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-vial"></i> Test Settings
                            </a>
                            <a href="{{ route('admin.settings.adminlte') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-palette"></i> AdminLTE Settings
                            </a>
                        </div>
                    </div>
                </div>
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
            // Initialize DataTable
            const table = $('#settings-table').DataTable({
                "pageLength": 25,
                "order": [[0, "asc"], [1, "asc"]], // Sort by group, then key
                "columnDefs": [
                    { "orderable": false, "targets": [4] } // Disable sorting on Actions column
                ],
                "language": {
                    "search": "Search settings:",
                    "lengthMenu": "Show _MENU_ settings per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ settings",
                    "emptyTable": "No settings found"
                }
            });

            // Custom search function for group filtering
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const selectedGroup = $('#group-filter').val();

                // If no group is selected, show all rows
                if (selectedGroup === '') {
                    return true;
                }

                // Get the row element to check data-group attribute
                const row = table.row(dataIndex).node();
                const rowGroup = $(row).attr('data-group');

                // Return true if the row's group matches the selected group
                return rowGroup === selectedGroup;
            });

            // Group filter functionality
            $('#group-filter').on('change', function() {
                table.draw(); // Redraw the table to apply the filter
            });            console.log('Settings table initialized with {{ count($settingsForTable) }} settings');
        });

        function deleteSetting(key) {
            if (confirm('Are you sure you want to delete the setting "' + key + '"? This action cannot be undone.')) {
                // Create form and submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/settings/' + encodeURIComponent(key);

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Copy setting key to clipboard
        function copyKey(key) {
            navigator.clipboard.writeText(key).then(function() {
                alert('Setting key copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }
    </script>
@stop
