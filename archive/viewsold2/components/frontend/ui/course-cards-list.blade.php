{{-- Course Cards List Component --}}
@props(['responsive' => 'desktop'])

@php
$courses = [
    [
        'courseId' => 1,
        'title' => 'Class D Security License',
        'description' => 'Comprehensive training for armed security professionals with firearms certification.',
        'icon' => 'class-d-icon.png',
        'detailUrl' => url('/courses/detail/1'),
        'fallbackPrice' => '$299.00'
    ],
    [
        'courseId' => 3,
        'title' => 'Class G Security License', 
        'description' => 'Essential training for unarmed security professionals and private investigators.',
        'icon' => 'class-g-icon.png',
        'detailUrl' => url('/courses/detail/2'),
        'fallbackPrice' => '$199.00'
    ]
];
@endphp

@foreach($courses as $courseData)
    <x-frontend.ui.course-card 
        :course-id="$courseData['courseId']"
        :title="$courseData['title']"
        :description="$courseData['description']"
        :icon="$courseData['icon']"
        :detail-url="$courseData['detailUrl']"
        :fallback-price="$courseData['fallbackPrice']"
        :responsive="$responsive"
    />
@endforeach
