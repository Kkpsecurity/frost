@php
    $gCourseCount = 0;
    $gCourseTitle = "G Course";

    foreach ($widgets['student_counts'] as $studentCount) {
        if ($studentCount->title === 'Florida G28') {
            $gCourseCount = $studentCount->count;
            $gCourseTitle = strtoupper($studentCount->title);
            break;
        }
    }
@endphp

<!-- small box -->
<div class="small-box bg-dark">
    <div class="inner">
        <h3>{{ $gCourseCount }}</h3>
        <span class="font-12"> {{ $gCourseTitle }}</span><br>
        <span class="opacity-50">{{ __('Total Student in G Class') }}</span>
    </div>
    <div class="icon">
        <i class="ion ion-person"></i>
    </div>
</div>
