@extends('adminlte::page')

@section('title', 'Edit Setting')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Setting</h1>
        <div>
            <a href="{{ route('admin.settings.show', $key) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Setting
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Setting: <code>{{ $key }}</code></h3>
                </div>
                <form action="{{ route('admin.settings.update', $key) }}" method="POST" id="settingForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Setting Key</label>
                            <input type="text" class="form-control" id="key" name="key" value="{{ $key }}" readonly>
                            <small class="form-text text-muted">The setting key cannot be changed. Create a new setting if needed.</small>
                        </div>

                        <div class="form-group">
                            <label for="value_type">Value Type</label>
                            <select class="form-control" id="value_type" name="value_type" onchange="updateValueInput()">
                                <option value="string" {{ $valueType === 'string' ? 'selected' : '' }}>String</option>
                                <option value="boolean" {{ $valueType === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                <option value="number" {{ $valueType === 'number' ? 'selected' : '' }}>Number</option>
                                <option value="json" {{ $valueType === 'json' ? 'selected' : '' }}>JSON/Array</option>
                            </select>
                            <small class="form-text text-muted">Select the appropriate data type for this setting.</small>
                        </div>

                        <div class="form-group" id="value-container">
                            <label for="value">Setting Value</label>

                            <!-- String Input -->
                            <div id="string-input" style="display: none;">
                                <textarea class="form-control" id="value_string" name="value" rows="3" placeholder="Enter string value">{{ $valueType === 'string' ? $value : '' }}</textarea>
                            </div>

                            <!-- Boolean Input -->
                            <div id="boolean-input" style="display: none;">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="bool_true" name="value" class="custom-control-input" value="1" {{ ($valueType === 'boolean' && $value) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="bool_true">True</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="bool_false" name="value" class="custom-control-input" value="0" {{ ($valueType === 'boolean' && !$value) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="bool_false">False</label>
                                </div>
                            </div>

                            <!-- Number Input -->
                            <div id="number-input" style="display: none;">
                                <input type="number" class="form-control" id="value_number" name="value" step="any" placeholder="Enter numeric value" value="{{ $valueType === 'number' ? $value : '' }}">
                            </div>

                            <!-- JSON Input -->
                            <div id="json-input" style="display: none;">
                                <textarea class="form-control" id="value_json" name="value" rows="8" placeholder="Enter valid JSON">{{ $valueType === 'json' ? (is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value) : '' }}</textarea>
                                <small class="form-text text-muted">Enter valid JSON format. The JSON will be validated before saving.</small>
                            </div>

                            @error('value')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Enter a description for this setting">{{ old('description', $description ?? '') }}</textarea>
                            <small class="form-text text-muted">Optional description to help understand the purpose of this setting.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Setting
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Value</h3>
                </div>
                <div class="card-body">
                    <h6>Type: <span class="badge badge-info">{{ ucfirst($valueType) }}</span></h6>
                    <h6>Current Value:</h6>
                    <div class="bg-light p-2 rounded">
                        @if($valueType === 'boolean')
                            <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                {{ $value ? 'TRUE' : 'FALSE' }}
                            </span>
                        @elseif($valueType === 'json')
                            <pre style="margin: 0; font-size: 12px;">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</pre>
                        @else
                            <code>{{ $value }}</code>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Validation Rules</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> String: Any text value</li>
                        <li><i class="fas fa-check text-success"></i> Boolean: True or False only</li>
                        <li><i class="fas fa-check text-success"></i> Number: Valid numeric value</li>
                        <li><i class="fas fa-check text-success"></i> JSON: Must be valid JSON format</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-info btn-sm" onclick="validateJson()" id="validate-json-btn" style="display: none;">
                            <i class="fas fa-check-circle"></i> Validate JSON
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="formatJson()" id="format-json-btn" style="display: none;">
                            <i class="fas fa-indent"></i> Format JSON
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Initialize the form on load
        document.addEventListener('DOMContentLoaded', function() {
            updateValueInput();
        });

        function updateValueInput() {
            const valueType = document.getElementById('value_type').value;

            // Hide all input types
            document.getElementById('string-input').style.display = 'none';
            document.getElementById('boolean-input').style.display = 'none';
            document.getElementById('number-input').style.display = 'none';
            document.getElementById('json-input').style.display = 'none';

            // Hide JSON buttons
            document.getElementById('validate-json-btn').style.display = 'none';
            document.getElementById('format-json-btn').style.display = 'none';

            // Show the selected input type
            document.getElementById(valueType + '-input').style.display = 'block';

            // Show JSON buttons for JSON type
            if (valueType === 'json') {
                document.getElementById('validate-json-btn').style.display = 'inline-block';
                document.getElementById('format-json-btn').style.display = 'inline-block';
            }
        }

        function validateJson() {
            const jsonValue = document.getElementById('value_json').value;
            try {
                JSON.parse(jsonValue);
                alert('Valid JSON!');
            } catch (e) {
                alert('Invalid JSON: ' + e.message);
            }
        }

        function formatJson() {
            const jsonTextarea = document.getElementById('value_json');
            try {
                const parsed = JSON.parse(jsonTextarea.value);
                jsonTextarea.value = JSON.stringify(parsed, null, 2);
            } catch (e) {
                alert('Cannot format invalid JSON: ' + e.message);
            }
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
                document.getElementById('settingForm').reset();
                updateValueInput();
            }
        }

        // Form validation before submit
        document.getElementById('settingForm').addEventListener('submit', function(e) {
            const valueType = document.getElementById('value_type').value;

            if (valueType === 'json') {
                const jsonValue = document.getElementById('value_json').value;
                try {
                    JSON.parse(jsonValue);
                } catch (error) {
                    e.preventDefault();
                    alert('Please enter valid JSON before submitting.');
                    return false;
                }
            }
        });
    </script>
@stop
