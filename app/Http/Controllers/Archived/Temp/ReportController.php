<?php

namespace App\Http\Controllers\Admin\Temp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use DB;
use App\RCache;
use App\Models\Exam;
use KKP\Laravel\PgTk;

use App\Traits\PageMetaDataTrait;


class ReportController extends Controller
{

    use PageMetaDataTrait;



    public function ExamIndex()
    {

        #dd( RCache::Exams() );

        $view    = 'admin.temp.exams';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $lesson_eq_counts = PgTk::toKVP( DB::select(<<<SQL
SELECT   lessons.id, COUNT( exam_questions.id )
FROM     lessons
JOIN     exam_questions ON exam_questions.lesson_id = lessons.id
GROUP BY lessons.id
ORDER BY lessons.id
SQL
    ), true );

        dd( $lesson_eq_counts );


        return view( $view, compact( 'content' ) );

    }

}
