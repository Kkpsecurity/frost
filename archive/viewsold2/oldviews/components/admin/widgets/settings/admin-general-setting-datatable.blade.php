<div class="table-responsive">
    <table id="settingsTable" class="table table-bordered table-striped table-hover mb-0 table-dark admin-dark-table"
        style="width: 100%;">
        <thead class="thead-dark">
            <tr>
                <th width="25%">
                    <i class="fas fa-key mr-1"></i>
                    Setting Key
                </th>
                <th width="30%">
                    <i class="fas fa-database mr-1"></i>
                    Current Value
                </th>
                <th width="12%">
                    <i class="fas fa-code mr-1"></i>
                    Type
                </th>
                <th width="13%">
                    <i class="fas fa-layer-group mr-1"></i>
                    Group
                </th>
                <th width="10%">
                    <i class="fas fa-signal mr-1"></i>
                    Status
                </th>
                <th width="10%">
                    <i class="fas fa-tools mr-1"></i>
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($settings as $setting)
                @php
                    // Get the setting data
                    $fullKey = $setting['key'];
                    $settingValue = $setting['value'];
                    $group = $setting['group'];

                    // Get the actual value type and display format
                    $valueType = gettype($settingValue);
                    $displayValue = '';
                    $badgeClass = 'badge-secondary';

                    if (is_array($settingValue) || is_object($settingValue)) {
                        $displayValue = json_encode($settingValue, JSON_PRETTY_PRINT);
                        $valueType = is_array($settingValue) ? 'array' : 'object';
                        $badgeClass = 'badge-info';
                    } elseif (is_bool($settingValue)) {
                        $displayValue = $settingValue ? 'true' : 'false';
                        $valueType = 'boolean';
                        $badgeClass = $settingValue ? 'badge-success' : 'badge-danger';
                    } elseif (is_numeric($settingValue)) {
                        $displayValue = $settingValue;
                        $valueType = is_int($settingValue) ? 'integer' : 'float';
                        $badgeClass = 'badge-primary';
                    } else {
                        $displayValue = $settingValue;
                        $valueType = 'string';
                        $badgeClass = 'badge-secondary';
                    }

                    // Truncate long values for display
                    $truncatedValue = strlen($displayValue) > 50 ? substr($displayValue, 0, 50) . '...' : $displayValue;

                    // Group badge styling
                    $groupBadgeClass = match($group) {
                        'general' => 'badge-warning',
                        'site' => 'badge-info',
                        'class' => 'badge-success',
                        'student' => 'badge-primary',
                        'instructor' => 'badge-danger',
                        'chat' => 'badge-secondary',
                        default => 'badge-dark'
                    };
                @endphp
                <tr data-group="{{ $group }}">
                    <td>
                        <div class="d-flex align-items-center">
                            <code class="setting-key">{{ $fullKey }}</code>
                            @if (strlen($displayValue) > 50)
                                <i class="fas fa-info-circle text-info ml-2" data-toggle="tooltip"
                                    data-html="true"
                                    title="<strong>Full Value:</strong><br>{{ htmlspecialchars($displayValue) }}"></i>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if (is_bool($settingValue))
                            <span class="badge {{ $badgeClass }} type-badge">
                                <i class="fas {{ $settingValue ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $displayValue }}
                            </span>
                        @elseif ($valueType === 'array' || $valueType === 'object')
                            <div class="d-flex align-items-center">
                                <i class="fas fa-code text-info mr-2"></i>
                                <span class="text-muted">
                                    {{ $valueType }} ({{ is_array($settingValue) ? count($settingValue) : 'object' }} items)
                                </span>
                            </div>
                        @else
                            <span class="setting-value">{{ $truncatedValue }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }} type-badge">
                            @switch($valueType)
                                @case('string')
                                    <i class="fas fa-quote-right"></i>
                                    @break
                                @case('integer')
                                @case('float')
                                    <i class="fas fa-hashtag"></i>
                                    @break
                                @case('boolean')
                                    <i class="fas fa-toggle-on"></i>
                                    @break
                                @case('array')
                                    <i class="fas fa-list"></i>
                                    @break
                                @case('object')
                                    <i class="fas fa-cube"></i>
                                    @break
                                @default
                                    <i class="fas fa-question"></i>
                            @endswitch
                            {{ $valueType }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $groupBadgeClass }} group-badge">
                            @switch($group)
                                @case('site')
                                    <i class="fas fa-globe"></i>
                                    @break
                                @case('class')
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    @break
                                @case('student')
                                    <i class="fas fa-user-graduate"></i>
                                    @break
                                @case('instructor')
                                    <i class="fas fa-user-tie"></i>
                                    @break
                                @case('chat')
                                    <i class="fas fa-comments"></i>
                                    @break
                                @default
                                    <i class="fas fa-cog"></i>
                            @endswitch
                            {{ ucfirst($group) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-success status-badge">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Setting Actions">
                            <a href="{{ route('admin.settings.show', $fullKey) }}"
                               class="btn btn-outline-info btn-sm"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="View Setting Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.settings.edit', $fullKey) }}"
                               class="btn btn-outline-warning btn-sm"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="Edit Setting Value">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.settings.destroy', $fullKey) }}"
                                  method="POST"
                                  style="display: inline-block;"
                                  onsubmit="return confirmDelete('{{ $fullKey }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Delete Setting (Cannot be undone)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
