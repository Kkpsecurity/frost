<form action="{{ route($options['parent_route']) }}" method="POST">
    @csrf
    @php
        $roles = App\Models\Role::all(); // Fetch all roles from the database
        $options = [
            'parent_route' => 'submitForm', // Example route name
        ];
    @endphp
    <div class="status-filter-container">
        <div class="input-group mb-3 status-filter">
            <select class="form-control form-control-lg" id="type_id" name="type_id">
                <option value="">Select a User Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    <!-- Adjust according to your Role model's attributes -->
                @endforeach
            </select>
            <div class="input-group-append">
                <button type="submit" class="btn btn-lg btn-default">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div>
</form>
