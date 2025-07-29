@php
    use App\Support\RoleManager;
    $roleOptions = RoleManager::getAdminRoleOptions();
@endphp

<div class="form-group mb-0 p-3">
    <label for="role-filter" class="form-label mb-1 admin-dark-text-primary">
        <i class="fas fa-filter"></i> Filter by Role:
    </label>
    <select id="role-filter" class="form-control form-control-sm admin-dark-filter" style="width: 200px;">
        @foreach($roleOptions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
