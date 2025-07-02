@php
    $dCourseCount = 0;
    $dCourseTitle = "40hr D Course";

    foreach ($widgets['student_counts'] as $studentCount) {
        if ($studentCount->title === 'Florida D40') {
            $dCourseCount = $studentCount->count;
            $dCourseTitle = strtoupper($studentCount->title);
            break;
        }
    }
@endphp

<!-- small box -->
<div class="small-box bg-gray">
    <div class="inner">
        <h3>{{ $dCourseCount }}</h3>
        <span class="font-12" style="font-size: 16px"> {{ $dCourseTitle }}</span><br>
        <span class="opacity-50">{{ __('Total Student in Class D') }}</span>
    </div>
    <div class="icon">
        <i class="ion ion-person"></i>
    </div>
</div>
