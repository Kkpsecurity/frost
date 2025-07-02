
<a href="{{ route('admin.center.adminusers.edit', $user->id) }}" class="btn btn-sm btn-primary edit-user" data-id="' . $user->id . '"><i class="fa fa-edit"></i></a>
<a href="{{ route('admin.center.adminusers.delete', $user->id) }}" class="btn btn-sm btn-danger delete-user" data-id="' . $user->id . '"><i class="fa fa-trash"></i></a>
