@php
    $headers = [
        __('ID'),
        __('First Name'),
        __('Last Name'),
        __('Email'),
        __('Role'),
        __('Status'),
        __('Action'),
    ];

    $rows = [];

    foreach($users as $user) {
        $row = [
            $user->id,
            $user->fname,
            $user->lname,
            $user->email,
            $user->role->name ?? 'Undefined',
            $user->is_active == 1 ? 'Active' : 'Disabled',
            view('admin.admin-center.admin-users.partials.admin_table_actions', ['user' => $user])->render(),
        ];
        array_push($rows, $row);
    }
@endphp

{{ \App\Support\LTEBootstrap::table($headers, $rows) }}

<div class="pull-right" style="padding: 20px;">
    {{ $users->links(config('define.pagination.style')) }}
</div>
