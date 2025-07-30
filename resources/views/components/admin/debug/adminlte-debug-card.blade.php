@props(['adminlteSettings' => []])

<div class="card card-dark mt-3" id="debug-card" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bug"></i> Debug Information
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" onclick="$('#debug-card').hide()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><strong>Database Settings (First 10):</strong></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-dark">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Database Value</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody id="database-debug">
                            @php
                                $count = 0;
                            @endphp
                            @foreach ($adminlteSettings as $key => $value)
                                @if ($count < 10)
                                    <tr>
                                        <td><code>{{ $key }}</code></td>
                                        <td>
                                            <span class="badge badge-info">
                                                @if (is_bool($value))
                                                    {{ $value ? 'Enabled' : 'Disabled' }}
                                                @elseif(in_array(strtolower($value), ['true', '1']))
                                                    Enabled
                                                @elseif(in_array(strtolower($value), ['false', '0']))
                                                    Disabled
                                                @elseif(is_null($value))
                                                    Not Set
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </td>
                                        <td><small>{{ gettype($value) }}</small></td>
                                    </tr>
                                    @php $count++; @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h6><strong>Config File Settings (First 10):</strong></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-dark">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Config Value</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $configSettings = config('adminlte');
                                $configCount = 0;
                            @endphp
                            @foreach ($configSettings as $key => $value)
                                @if ($configCount < 10)
                                    <tr>
                                        <td><code>{{ $key }}</code></td>
                                        <td>
                                            <span class="badge badge-warning">
                                                @if (is_bool($value))
                                                    {{ $value ? 'Enabled' : 'Disabled' }}
                                                @elseif(is_null($value))
                                                    Not Set
                                                @elseif(is_array($value))
                                                    Array ({{ count($value) }} items)
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </td>
                                        <td><small>{{ gettype($value) }}</small></td>
                                    </tr>
                                    @php $configCount++; @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <h6><strong>Settings Count Summary:</strong></h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-database text-info"></i> Database Settings:
                        <strong>{{ count($adminlteSettings) }}</strong></li>
                    <li><i class="fas fa-cog text-warning"></i> Config Settings:
                        <strong>{{ count(config('adminlte')) }}</strong></li>
                    <li><i class="fas fa-question text-danger"></i> Missing from DB:
                        <strong>{{ count(array_diff_key(config('adminlte'), $adminlteSettings)) }}</strong></li>
                    <li><i class="fas fa-plus text-success"></i> Extra in DB:
                        <strong>{{ count(array_diff_key($adminlteSettings, config('adminlte'))) }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
