<style>
    .card {
        border: none;
        border-radius: 0;
        margin-bottom: 10px;
    }

    .num-text {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .question {
        min-height: 30px;
        height: auto;
        font-size: 1.2rem;
        font-weight: 700;
        width: 100%;
    }

    .list-group-item {
        border: none;
        width: 100%;
    }

    .list-group-item .form-check {
        width: 100%;
    }

    .list-group-item .form-check .form-check-input {
        width: 20px;
        height: 20px;
    }

    .list-group-item .form-check .form-check-label {
        font-size: 1.2rem;
        font-weight: 500;
        margin-left: 10px;
    }

    .list-group-item .form-check .form-check-input:checked+.form-check-label {
        color: #0d6efd;
    }
</style>
<div class="card">
    <div class="card-header">
        <div class="input-group mb-2 flex-nowrap">
            <div class="num-text">
                <span>{{ $qnum }}</span>
            </div>
            <div class="question ms-2 text-left">{{ $ExamQuestion->question }}</div>
        </div>
    </div>

    <ul class="list-group">
        {{-- loop answers --}}
        @for ($anum = 1; $anum <= 5; $anum++)
            {{-- is answer defined --}}
            @if ($ExamQuestion->{"answer_{$anum}"})
                <?php
                $answer_name = "answer_{$ExamQuestion->id}";
                $radio_id = "answer_{$ExamQuestion->id}_{$anum}";
                if (Auth::user()->IsAdministrator()) {
                    $checked = $ExamQuestion->correct == $anum;
                } else {
                    $checked = old($answer_name) == $anum;
                }
                ?>
                <li class="list-group-item">
                    <div class="form-check mb-2 flex-nowrap">
                        <input class="form-check-input" type="radio" name="{{ $answer_name }}" id="{{ $radio_id }}"
                            value="{{ $anum }}" @if ($checked) checked @endif required>
                        <label class="form-check-label"
                            for="{{ $radio_id }}">{{ $ExamQuestion->{"answer_{$anum}"} }}</label>
                    </div>
                </li>
            @endif
        @endfor
    </ul>
</div>

Disable Cache
26 requests
1.36 MB / 995.21 kB transferred
Finish: 17.38 s
DOMContentLoaded: 637 ms
load: 762 ms


success true
data { student: {…}, courses: (2)[…], progress: {…}, … }
student { id: 2, fname: "Richard", lname: "Clark", … }
courses [ {…}, {…} ]
progress { total_courses: 2, completed: 0, in_progress: 0 }
validations_by_course_auth { 2: {…}, 10862: {…} }
active_classroom { status: "waiting", course_id: 1, course_auth_id: 2, … }
studentExamsByCourseAuth { 2: {…}, 10862: {…} }
2 { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
is_ready true
next_attempt_at null
missing_id_file false
has_active_attempt false
exam_auth_id null
exam_id 1
num_questions 170
num_to_pass 128
policy_expire_seconds 14400
_debug_has_exam true
_debug_course_id 1
10862 { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
studentExam { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
studentUnit null
studentLessons []
notifications []
assignments []
challenges []

Disable Cache
26 requests
1.36 MB / 995.21 kB transferred
Finish: 17.38 s
DOMContentLoaded: 637 ms
load: 762 ms


success true
data { student: {…}, courses: (2)[…], progress: {…}, … }
student { id: 2, fname: "Richard", lname: "Clark", … }
courses [ {…}, {…} ]
progress { total_courses: 2, completed: 0, in_progress: 0 }
validations_by_course_auth { 2: {…}, 10862: {…} }
active_classroom { status: "waiting", course_id: 1, course_auth_id: 2, … }
studentExamsByCourseAuth { 2: {…}, 10862: {…} }
2 { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
is_ready true
next_attempt_at null
missing_id_file false
has_active_attempt false
exam_auth_id null
exam_id 1
num_questions 170
num_to_pass 128
policy_expire_seconds 14400
_debug_has_exam true
_debug_course_id 1
10862 { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
studentExam { is_ready: true, missing_id_file: false, has_active_attempt: false, … }
studentUnit null
studentLessons []
notifications []
assignments []
challenges []
