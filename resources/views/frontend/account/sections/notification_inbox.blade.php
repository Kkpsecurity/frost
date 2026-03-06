{{-- Notification Inbox Section --}}
<div class="notification-inbox-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-white mb-0">
            <i class="fas fa-bell me-2"></i>{{ __('frontend.account.my_notifications') }}
        </h3>
        @if ($user->unreadNotifications->count())
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-check-double me-1"></i>{{ __('frontend.account.mark_all_read') }}
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

        // Batch-resolve course names from course_date_ids present in this page.
        $courseDateIds = $allNotifications
            ->pluck('data')
            ->map(fn($d) => $d['course_date_id'] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $courseNameMap = [];
        if (!empty($courseDateIds)) {
            \App\Models\CourseDate::with(['CourseUnit.Course'])
                ->whereIn('id', $courseDateIds)
                ->get()
                ->each(function ($cd) use (&$courseNameMap) {
                    $courseNameMap[(int) $cd->id] = $cd->CourseUnit?->Course?->title ?? null;
                });
        }
    @endphp

    @if ($allNotifications->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-secondary mb-3"></i>
            <p class="text-white-50">{{ __('frontend.account.no_notifications') }}</p>
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
                    $deleteUrl = route('notifications.delete', $notification->id);
                    $itemTarget = $url ?? $readUrl;

                    // Context chips
                    $courseDateId = isset($data['course_date_id']) ? (int) $data['course_date_id'] : null;
                    $courseName = $courseDateId ? $courseNameMap[$courseDateId] ?? null : null;
                    $lessonName = $data['lesson_name'] ?? null;
                @endphp

                <div class="list-group-item bg-dark border-secondary mb-2 rounded px-3 py-2
                            {{ $isUnread ? 'border-start border-3 border-' . $color : '' }}"
                    style="{{ $isUnread ? 'background-color: rgba(255,255,255,0.05) !important;' : '' }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">

                        {{-- Left: icon + body --}}
                        <a href="{{ $itemTarget }}"
                            class="d-flex align-items-start gap-3 text-decoration-none flex-grow-1 pe-2">
                            <div class="mt-1 flex-shrink-0">
                                <i class="fas fa-{{ $icon }} text-{{ $color }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 {{ $isUnread ? 'text-white fw-semibold' : 'text-white-50' }}">
                                    {{ $title }}
                                    @if ($isUnread)
                                        <span class="badge bg-{{ $color }} ms-1" style="font-size:0.65rem;">
                                            {{ __('frontend.account.badge_new') }}
                                        </span>
                                    @endif
                                </h6>

                                @if ($message)
                                    <p class="mb-1 small {{ $isUnread ? 'text-white-50' : 'text-secondary' }}">
                                        {{ $message }}
                                    </p>
                                @endif

                                {{-- Course / Lesson context chips --}}
                                @if ($courseName || $lessonName)
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @if ($courseName)
                                            <span class="badge rounded-pill"
                                                style="background:rgba(52,152,219,0.25);color:#7ec8e3;font-size:0.7rem;">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $courseName }}
                                            </span>
                                        @endif
                                        @if ($lessonName)
                                            <span class="badge rounded-pill"
                                                style="background:rgba(46,204,113,0.2);color:#82e0aa;font-size:0.7rem;">
                                                <i class="fas fa-book-open me-1"></i>{{ $lessonName }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </a>

                        {{-- Right: time + delete --}}
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0 ms-2">
                            <small class="text-secondary text-nowrap">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                            <form action="{{ $deleteUrl }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm p-0" title="Delete"
                                    style="color:#e74c3c;line-height:1;">
                                    <i class="fas fa-trash-alt" style="font-size:0.75rem;"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
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
            <i class="fas fa-cog me-1"></i>{{ __('frontend.account.notification_preferences') }}
        </a>
    </div>
</div>
