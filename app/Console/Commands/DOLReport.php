<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file DOLReport.php
 * @brief Command to generate DOL report for course auths.
 * @details Lists course auths created within a specific date range and generates PDF records.
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Helpers\PgTk;
use App\Classes\Frost\DOLRecordPDF;


class DOLReport extends Command
{

    protected $signature   = 'command:dolreport';
    protected $description = 'DOL Report';


    public function handle(): int
    {

        if (app()->environment('production')) {
            $this->error('Refusing to run on production server');
            return 0;
        }


        $sql = <<<SQL
SELECT DISTINCT( course_auth_id )
FROM   student_unit
WHERE  created_at >= '2024-01-01 00:00:00-04'
AND    created_at <= '2024-04-20 00:00:00-04'
AND    course_auth_id NOT IN (
    SELECT id
    FROM   course_auths
    WHERE  user_id < 10000
)
SQL;


        foreach (PgTk::toSimple(DB::select(DB::raw($sql))) as $course_auth_id) {
            $res = (new DOLRecordPDF)->GenPDF($course_auth_id);
            $this->line($res);
        }


        return 1;
    }
}
