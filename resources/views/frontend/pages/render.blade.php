@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@section('content')

    @php
        $courses = \App\RCache::Courses()->where('is_active', true);

        $welcomeTitle = 'Welcome to The Security Training Group';
        $welcomeSubtitle = 'Florida Class D Security License and Armed Statewide Firearms Class G License';
        $classDIcon = asset('assets/img/icon/online-course-icon-class-d.png');
        $classGIcon = asset('assets/img/icon/online-course-icon-class-g.png');
        $courseDTitle = 'Florida Class D Security License Course';
        $courseDInfo = 'Comprehensive online training program. Flexible schedule.';
        $courseDPrice = '$125.00 USD';
        $courseGTitle = 'Armed Statewide Firearms Class G License Course';
        $courseGInfo = 'Combination of online learning and in-person range training.';
        $courseGPrice = '$250.00 USD';
        $content1 = 'The Security Training Groups Online Security Training Program offers comprehensive courses for individuals seeking the Florida Class D Security License and the Armed Statewide Firearms Class G License. With the flexibility and convenience of online learning, students can now access high-quality training from anywhere in Florida.';
        $content2 = 'The Florida Class D Security License course provides in-depth training on essential aspects of security operations, including legal guidelines, emergency response procedures, communication skills, and ethical conduct. Our expert instructors deliver engaging lessons that equip students with the knowledge and skills required to excel as security professionals.';
    @endphp

    <x-panels.welcome-login-slider :title="$welcomeTitle" :subtitle="$welcomeSubtitle" :class-d-icon="$classDIcon" :class-g-icon="$classGIcon" :course-d-title="$courseDTitle" :course-d-info="$courseDInfo" :course-d-price="$courseDPrice" :course-g-title="$courseGTitle" :course-g-info="$courseGInfo" :course-g-price="$courseGPrice" :content1="$content1" :content2="$content2" />

    <x-panels.getting-started />

@stop
