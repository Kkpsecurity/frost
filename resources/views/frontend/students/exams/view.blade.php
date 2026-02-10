@extends('layouts.app')

@section('content')
    @include('frontend.partials.breadcrumbs')
    <section class="dashboard bg-light" style="margin-top: -40px;  min-height: 700px; height: auto;">
        <div class="container shadow">
            <div class="row">
                <div class="col-md-12 mt-3 bg-light">
                    <h3 class="text-left">Exam: <?php echo $ExamAuthObj->Course->ShortTitle(); ?></h3>
                    <p class="text-left">Total Questions: <?php echo $ExamAuthObj->ExamQuestions->count(); ?></p>
                    <p>Must Submit Exam Before: <b><?= $ExamAuthObj->ExamAuth->ExpiresAt('ddd MM/DD HH:mm') ?></b></p>
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
                        {{ Form::submit('Submit', ['class' => 'btn btn-primary float-end mb-5']) }}
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
