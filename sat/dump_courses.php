<?php


function sat_dump_courses()
{


    $lesson_eq_counts = PgTk::toKVP( DB::select(<<<SQL
SELECT   lessons.id, COUNT( exam_questions.id )
FROM     lessons
JOIN     exam_questions ON exam_questions.lesson_id = lessons.id
GROUP BY lessons.id
ORDER BY lessons.id
SQL
    ), true );


    //
    // prepare table
    //

    $html = <<<HTML
<table border="0" cellspacing="3" cellpadding="4">

HTML;


    foreach ( RCache::Courses() as $Course )
    {

        $course_progress_minutes = 0;
        $course_exam_questions   = 0;

        $html .= <<<ROW
<tr class="header">
  <td colspan="6">{$Course->title_long}</td>
</tr>

ROW;

        foreach ( $Course->GetCourseUnits() as $CourseUnit )
        {

            $lessons_minutes    = 0;
            $progress_minutes   = 0;
            $instructor_minutes = 0;
            $exam_questions     = 0;

            $html .=<<<ROW
<tr>
  <td colspan="2" class="subheader"><b>{$CourseUnit->title} ({$CourseUnit->admin_title})</b></td>
  <th class="subheader">Lsn</th>
  <th class="subheader">Prg</th>
  <th class="subheader">Inst</th>
  <th class="subheader">EQ</th>
</tr>

ROW;

            foreach ( $CourseUnit->GetCourseUnitLessons() as $CourseUnitLesson )
            {

                $Lesson  = RCache::Lessons( $CourseUnitLesson->lesson_id );
                $EQCount = $lesson_eq_counts->{$Lesson->id} ?? 0;

                $lessons_minutes    += $Lesson->credit_minutes;
                $progress_minutes   += $CourseUnitLesson->progress_minutes;
                $instructor_minutes += $CourseUnitLesson->instr_seconds / 60;
                $exam_questions     += $EQCount;

                $course_progress_minutes += $CourseUnitLesson->progress_minutes;
                $course_exam_questions   += $EQCount;

                $html .=<<<ROW
<tr>
  <td>{$Lesson->id}</td>
  <td>{$Lesson->title}</td>
  <td align="center">{$Lesson->CreditHours()}</td>
  <td align="center">{$CourseUnitLesson->ProgressHours()}</td>
  <td align="center">{$CourseUnitLesson->InstructorHours()}</td>
  <td align="center">{$EQCount}</td>
</tr>

ROW;

            }

            $lesson_hours     = sprintf( '%0.1f', $lessons_minutes    / 60 );
            $progress_hours   = sprintf( '%0.1f', $progress_minutes   / 60 );
            $instructor_hours = sprintf( '%0.1f', $instructor_minutes / 60 );

            $error = ( $lesson_hours != $progress_hours or $progress_hours != $instructor_hours )
                   ? ' color: red;' : '';

            $html .=<<<ROW
<tr>
  <td colspan="2" align="right" style="font-weight: bold;">Totals</td>
  <td align="center" style="font-weight: bold;{$error}">{$lesson_hours}</td>
  <td align="center" style="font-weight: bold;{$error}">{$progress_hours}</td>
  <td align="center" style="font-weight: bold;{$error}">{$instructor_hours}</td>
  <td align="center" style="font-weight: bold;">{$exam_questions}</td>
</tr>

ROW;

        }


        $Exam = $Course->GetExam();
        $course_progress_hours = sprintf( '%0.1f', $course_progress_minutes / 60 );

        $html .= <<<ROW
<tr style="background-color: #ffc;">
  <td colspan="2">
    <b>ExamID: {$Exam->id}</b>
    &nbsp;
    ({$Exam->admin_title})
    &nbsp;
    {$Exam->num_to_pass} / {$Exam->num_questions}
    &nbsp;
    {$Exam->Minutes()} min
  </td>
  <td>&nbsp;</th>
  <th>{$course_progress_hours}</th>
  <td>&nbsp;</th>
  <td align="center">{$course_exam_questions}</td>
</tr>

ROW;

    }



    //
    // end table
    //

    return SAT_Header() . "{$html}</table>\n" . SAT_Footer();

}
