
<a href="{{ route('admin.admin-center.adminusers.edit', $user->id) }}" class="btn btn-sm btn-primary edit-user m-1" data-id="{{ $user->id }}"><i class="fa fa-edit"></i></a>
<?php if(Auth::user()->IsSysAdmin() && $user->id !== Auth::user()->id): ?>
<a href="{{ route('admin.admin-center.impersonate', $user->id) }}" class="btn btn-sm btn-warning impersonate-user m-1" data-id="{{ $user->id }}" title="Impersonate User"><i class="fa fa-user-secret"></i></a>
<?php endif; ?>
<a href="{{ route('admin.admin-center.adminusers.delete', $user->id) }}" class="btn btn-sm btn-danger delete-user m-1" data-id="{{ $user->id }}"><i class="fa fa-trash"></i></a>
