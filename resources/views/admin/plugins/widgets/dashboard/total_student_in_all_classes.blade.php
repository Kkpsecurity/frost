@php
    $totalCount = 0;
    $totalTitle = "All Students";

    foreach ($widgets['student_counts'] as $studentCount) {
        if ($studentCount->title === 'Total') {
            $totalCount = $studentCount->count;
            $totalTitle = strtoupper($studentCount->title);
            break;
        }
    }
@endphp

<!-- small box -->
<div class="small-box bg-success">
    <div class="inner">
        <h3>{{ $totalCount }}</h3>
        <span class="font-12"> {{ $totalTitle }}</span><br>
        <span class="opacity-50">{{ __('Total Student in All Classes') }}</span>
    </div>
    <div class="icon">
        <i class="ion ion-person"></i>
    </div>
</div>
