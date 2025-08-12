@php
    $supportStats = [
        [
            'title' => 'Open Tickets',
            'count' => '23',
            'icon' => 'fas fa-ticket-alt',
            'color' => 'primary',
            'linkText' => 'View Tickets'
        ],
        [
            'title' => 'Resolved Today',
            'count' => '156',
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'linkText' => 'View Resolved'
        ],
        [
            'title' => 'Urgent Tickets',
            'count' => '8',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'warning',
            'linkText' => 'View Urgent'
        ],
        [
            'title' => 'Avg Response',
            'count' => '2.4h',
            'icon' => 'fas fa-clock',
            'color' => 'info',
            'linkText' => 'View Stats'
        ],
        [
            'title' => 'Active Agents',
            'count' => '12',
            'icon' => 'fas fa-user-headset',
            'color' => 'success',
            'linkText' => 'View Agents'
        ],
        [
            'title' => 'Knowledge Base',
            'count' => '89',
            'icon' => 'fas fa-book',
            'color' => 'secondary',
            'linkText' => 'View Articles'
        ]
    ];
@endphp

<div class="row">
    @foreach($supportStats as $stat)
        <x-admin.dashboard.mini-stats-card
            :title="$stat['title']"
            :count="$stat['count']"
            :icon="$stat['icon']"
            :color="$stat['color']"
            :linkText="$stat['linkText']"
        />
    @endforeach
</div>
