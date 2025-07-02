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
