{{-- Footer Courses Links Component --}}
<div class="footer-content p-3">
    <h4 class="text-white">{{ __('frontend.footer.courses') }}</h4>
    <ul class="list-unstyled">
        @foreach (App\Services\RCache::Courses()->where('is_active', true) as $course)
            <li><a href="{{ route('courses.show', $course->id) }}" class="text-white-50">{{ $course->title_long }}</a>
            </li>
        @endforeach
    </ul>
</div>
