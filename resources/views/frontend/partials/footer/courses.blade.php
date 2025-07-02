<div class="footer-content p-3">
    <h4 style="color: #f8f9fa;">Courses</h4>
    <ul class="list-unstyled">
        @foreach (RCache::Courses()->where( 'is_active', true ) as $course)
            <li><a href="{{ url('courses/detail/' . $course->id) }}" style="color: #f8f9fa;">{{ $course->title_long }}</a></li>
        @endforeach
    </ul>
</div>
