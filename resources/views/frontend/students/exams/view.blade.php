@extends('layouts.app')

@section('content')
    @include('frontend.partials.breadcrumbs')
    <section class="dashboard bg-light" style="margin-top: -40px;  min-height: 700px; height: auto;">
        <div class="container shadow">
            <div class="row">
                <div class="col-md-12 mt-3 bg-light">
                    <h3 class="text-left">{{ __('frontend.exam.exam_label') }} {{ $ExamAuthObj->Course->ShortTitle() }}</h3>
                    <p class="text-left">{{ __('frontend.exam.total_questions') }} {{ $ExamAuthObj->ExamQuestions->count() }}
                    </p>
                    <p>{{ __('frontend.exam.must_submit_before') }}
                        <b>{{ $ExamAuthObj->ExamAuth->ExpiresAt('ddd MM/DD HH:mm') }}</b></p>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    {{ Form::open(['route' => ['classroom.exam.score', $ExamAuthObj->ExamAuth], 'id' => 'exam', 'autocomplete' => 'off']) }}
                    {{ Form::hidden('exam_id', $ExamAuthObj->ExamAuth->id) }}

                    @php $qnum = 0; @endphp
                    @foreach ($ExamAuthObj->ExamQuestions as $ExamQuestion)
                        @include('frontend/students/exam/question', ['qnum' => ++$qnum])
                    @endforeach

                    <div class="form-group">
                        {{ Form::submit(__('frontend.exam.submit'), ['class' => 'btn btn-primary float-end mb-5']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

mandala.shakti.007@gmail.com
#!$c*ytmDeQNp9q3vp#7wQyp
clarkrv@kkpsecuritygroup.com
