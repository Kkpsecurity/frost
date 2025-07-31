<div class="table-responsive">
    <table id="settingsTable" class="table table-bordered table-striped table-hover mb-0 table-dark admin-dark-table"
        style="width: 100%;">
        <thead class="thead-dark">
            <tr>
                <th width="20%">Setting Key</th>
                <th width="30%">Current Value</th>
                <th width="15%">Type</th>
                <th width="15%">Group</th>
                <th width="10%">Status</th>
                <th width="10%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($settings as $prefix => $settingsGroup)
                @foreach ($settingsGroup as $key => $value)
                    @php
                        $fullKey = $prefix === 'general' ? $key : $prefix . '.' . $key;
                        $valueType = gettype($value);
                        $displayValue = '';
                        $badgeClass = 'badge-secondary';

                        if (is_array($value) || is_object($value)) {
                            $displayValue = json_encode($value, JSON_PRETTY_PRINT);
                            $valueType = is_array($value) ? 'array' : 'object';
                            $badgeClass = 'badge-info';
                        } elseif (is_bool($value)) {
                            $displayValue = $value ? 'true' : 'false';
                            $valueType = 'boolean';
                            $badgeClass = $value ? 'badge-success' : 'badge-danger';
                        } elseif (is_numeric($value)) {
                            $displayValue = $value;
                            $valueType = is_int($value) ? 'integer' : 'float';
                            $badgeClass = 'badge-primary';
                        } else {
                            $displayValue = $value;
                            $valueType = 'string';
                            $badgeClass = 'badge-secondary';
                        }

                        // Truncate long values
                        $truncatedValue =
                            strlen($displayValue) > 50 ? substr($displayValue, 0, 50) . '...' : $displayValue;

                        // Group badge class
                        $groupBadgeClass = $prefix === 'general' ? 'badge-warning' : 'badge-info';
                    @endphp
                    <tr data-group="{{ $prefix }}">
                        <td>
                            <code class="text-primary">{{ $key }}</code>
                            @if (strlen($displayValue) > 50)
                                <i class="fas fa-info-circle text-muted ml-1" data-toggle="tooltip"
                                    title="{{ htmlspecialchars($displayValue) }}"></i>
                            @endif
                        </td>
                        <td>
                            @if (is_bool($value))
                                <span class="badge {{ $badgeClass }}">
                                    <i class="fas {{ $value ? 'fa-check' : 'fa-times' }}"></i>
                                    {{ $displayValue }}
                                </span>
                            @elseif ($valueType === 'array' || $valueType === 'object')
                                <span class="text-muted">
                                    <i class="fas fa-code"></i>
                                    {{ $valueType }} ({{ is_array($value) ? count($value) : 'object' }} items)
                                </span>
                            @else
                                <span class="setting-value">{{ $truncatedValue }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $valueType }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $groupBadgeClass }}">{{ ucfirst($prefix) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.settings.show', $fullKey) }}" class="btn btn-info btn-xs"
                                    data-toggle="tooltip" title="View Setting">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.settings.edit', $fullKey) }}" class="btn btn-warning btn-xs"
                                    data-toggle="tooltip" title="Edit Setting">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.settings.destroy', $fullKey) }}" method="POST"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Are you sure you want to delete this setting?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip"
                                        title="Delete Setting">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
