<div class="form-group mb-0">
    <label for="role-filter" class="form-label mb-1">
        <i class="fas fa-filter"></i> Filter by Role:
    </label>
    <select id="role-filter" class="form-control form-control-sm" style="width: 200px;">
        @foreach($roleOptions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
