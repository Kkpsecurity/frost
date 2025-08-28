<div class="course-curriculum-section mb-5">
    <h3 class="section-title text-white">What You'll Learn</h3>
    <div class="row">
        <div class="col-lg-6">
            <ul class="feature-list-detailed">
                @if (isset($course['features']) && is_array($course['features']))
                    @foreach (array_slice($course['features'], 0, ceil(count($course['features']) / 2)) as $feature)
                        <li class="text-white-50">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            {{ $feature }}
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="col-lg-6">
            <ul class="feature-list-detailed">
                @if (isset($course['features']) && is_array($course['features']))
                    @foreach (array_slice($course['features'], ceil(count($course['features']) / 2)) as $feature)
                        <li class="text-white-50">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            {{ $feature }}
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>

{{-- Course Curriculum Section (using real course units) --}}
@if (isset($course['courseUnits']) && $course['courseUnits']->count() > 0)
    <div class="course-curriculum-detailed mb-5">
        <h3 class="section-title text-white">Course Curriculum</h3>
        <div class="curriculum-content">
            @foreach ($course['courseUnits'] as $index => $unit)
                <div class="curriculum-unit mb-3 p-3 rounded"
                    style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <h5 class="unit-title text-info mb-2">
                        <span class="unit-number badge bg-info text-dark me-2">{{ $index + 1 }}</span>
                        {{ $unit->title }}
                    </h5>
                    @if ($unit->admin_title && $unit->admin_title !== $unit->title)
                        <p class="unit-subtitle text-white-50 small mb-2">
                            {{ $unit->admin_title }}</p>
                    @endif
                    @if (isset($unit->curriculum_content))
                        <p class="unit-description text-white-50 mb-0">
                            {{ $unit->curriculum_content }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
