@php
    $userStats = [
        [
            'title' => 'Total Users',
            'count' => \App\Models\User::count(),
            'icon' => 'fas fa-users',
            'color' => 'info',
            'link' => '#',
            'linkText' => 'Manage Users'
        ],
        [
            'title' => 'Total Admins',
            'count' => \App\Models\User::whereIn('role_id', [1, 2])->count(),
            'icon' => 'fas fa-user-shield',
            'color' => 'success',
            'link' => '#',
            'linkText' => 'Manage Admins'
        ],
        [
            'title' => 'Instructors',
            'count' => \App\Models\User::where('role_id', 3)->count(),
            'icon' => 'fas fa-chalkboard-teacher',
            'color' => 'warning',
            'link' => '#',
            'linkText' => 'Manage Instructors'
        ],
        [
            'title' => 'Support Staff',
            'count' => \App\Models\User::where('role_id', 4)->count(),
            'icon' => 'fas fa-headset',
            'color' => 'danger',
            'link' => '#',
            'linkText' => 'Manage Support'
        ]
    ];
@endphp

<div class="row">
    @foreach($userStats as $stat)
        <x-admin.dashboard.stats-card
            :title="$stat['title']"
            :count="$stat['count']"
            :icon="$stat['icon']"
            :color="$stat['color']"
            :link="$stat['link']"
            :linkText="$stat['linkText']"
        />
    @endforeach
</div>
