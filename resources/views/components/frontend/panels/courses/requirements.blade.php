<div class="course-requirements-section mb-5">
    <h3 class="section-title text-white">Requirements</h3>
    <div class="requirements-content">
        <ul class="requirements-list">
            @if (isset($course['requirements']))
                @foreach ($course['requirements'] as $requirement)
                    <li class="text-white-50"><i
                            class="fas fa-exclamation-circle text-warning me-2"></i>{{ $requirement }}
                    </li>
                @endforeach
            @else
                <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Must be 18 years
                    or older</li>
                <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Valid
                    government-issued photo ID required</li>
                <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Background check
                    may be required</li>
                <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>High school
                    diploma or equivalent preferred</li>
            @endif
        </ul>
    </div>
</div>
