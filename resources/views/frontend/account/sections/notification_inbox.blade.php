{{-- Notification Inbox Section --}}
<div class="notification-inbox-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white mb-0">
            <i class="fas fa-bell me-2"></i>My Notifications
        </h3>
        @if ($user->unreadNotifications->count())
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-check-double me-1"></i>Mark All Read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $allNotifications = $user->notifications()->latest()->paginate(25);
    @endphp

    @if ($allNotifications->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-secondary mb-3"></i>
            <p class="text-white-50">You have no notifications yet.</p>
        </div>
    @else
        <div class="list-group list-group-flush">
            @foreach ($allNotifications as $notification)
                @php
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $url = $data['url'] ?? null;
                    $icon = $data['icon'] ?? 'bell';
                    $color = $data['priority_color'] ?? 'secondary';
                    $isUnread = is_null($notification->read_at);
                    $readUrl = route('notifications.mark-read', $notification->id);
                    $itemTarget = $url ?? $readUrl;
                @endphp
                <a href="{{ $itemTarget }}"
                    class="list-group-item list-group-item-action bg-dark border-secondary mb-2 rounded
                          {{ $isUnread ? 'border-start border-3 border-' . $color : '' }}"
                    style="{{ $isUnread ? 'background-color: rgba(255,255,255,0.05) !important;' : '' }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex align-items-start gap-3">
                            <div class="mt-1">
                                <i class="fas fa-{{ $icon }} text-{{ $color }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 {{ $isUnread ? 'text-white fw-semibold' : 'text-white-50' }}">
                                    {{ $title }}
                                    @if ($isUnread)
                                        <span class="badge bg-{{ $color }} ms-1"
                                            style="font-size:0.65rem;">NEW</span>
                                    @endif
                                </h6>
                                @if ($message)
                                    <p class="mb-0 small {{ $isUnread ? 'text-white-50' : 'text-secondary' }}">
                                        {{ $message }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <small class="text-secondary text-nowrap ms-3">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($allNotifications->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $allNotifications->appends(['section' => 'inbox'])->links() }}
            </div>
        @endif
    @endif

    <div class="mt-4 pt-3 border-top border-secondary">
        <a href="{{ route('account.index', ['section' => 'notifications']) }}" class="text-white-50 small">
            <i class="fas fa-cog me-1"></i>Notification Preferences
        </a>
    </div>
</div>
