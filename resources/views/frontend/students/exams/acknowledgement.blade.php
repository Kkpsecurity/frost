@extends('layouts.app')

@section('content')
    <style>
        .begin-exam-btn {
            font-size: 1.5rem;
            padding: 10px 20px;
            border-radius: 20px;
        }
    </style>

    @include('frontend.partials.breadcrumbs')

    <section class="dashboard bg-light" style="margin-top: -40px;  min-height: 700px; height: auto;">
        <div class="container">

            <div class="bg-light p-5 rounded shadow" style="height: 620px; margin-top: 40px;">
                <h2 class="title fw-bold">{{ __('frontend.exam.beginning_exam') }}</h2>
                <h4 class="sub-title lead fw-bold">{{ __('frontend.exam.begin_exam_instructions') }}</h4>

                <div class="alert alert-secondary">
                    {{ __('frontend.exam.time_limit_notice', ['time' => $Exam->ExamTime()]) }}
                </div>

                <div class="list-group">
                    <li class="list-group-item">{{ __('frontend.exam.total_questions') }} <b>{{ $Exam->num_questions }}</b>
                    </li>
                    <li class="list-group-item">{{ __('frontend.exam.required_to_pass') }} <b>{{ $Exam->num_to_pass }}</b>
                    </li>
                </div>
                <center>
                    <a href="{{ route('classroom.exam.authorize', [$CourseAuth, true]) }}"
                        class="btn btn-success begin-exam-btn btn-xl mt-3">{{ __('frontend.exam.begin_exam') }}</a>
                </center>
            </div>






            {{--
            <div class="row">
                <div class="col-md-12 mt-3">
                    <h3 class="text-left">Exam: {{ $ExamAuthObj->Course->ShortTitle() }}</h3>
                    <p class="text-left">Total Questions: {{ $ExamAuthObj->ExamQuestions->count() }}</p>
                </div>
            </div>
--}}

            {{--
            <div class="row">
                <div class="col-12">
                    {{ Form::open(['route' => ['classroom.exam.score', $ExamAuthObj->ExamAuth], 'id' => 'exam', 'autocomplete' => 'off']) }}
                    {{ Form::hidden('exam_id', $ExamAuthObj->ExamAuth->id) }}

                    @php $qnum = 0; @endphp
                    @foreach ($ExamAuthObj->ExamQuestions as $ExamQuestion)
                        @include('frontend/students/exam/question', ['qnum' => ++$qnum])
                    @endforeach

                    <div class="form-group">
                        {{ Form::submit('Submit', ['class' => 'btn btn-primary float-end mb-5']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
--}}
        </div>
    </section>
@endsection
