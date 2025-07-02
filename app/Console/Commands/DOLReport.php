<?php
declare(strict_types=1);

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

use App\Classes\DOLRecords\DOLRecordPDF;
use KKP\Laravel\PgTk;


class DOLReport extends Command
{

    protected $signature   = 'command:dolreport';
    protected $description = 'DOL Report';


    public function handle() : int
    {

        if ( app()->environment( 'production' ) )
        {
            $this->error( 'Refusing to run on production server' );
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


        foreach ( PgTk::toSimple( DB::select( DB::raw( $sql ) ) ) as $course_auth_id )
        {
            $res = ( new DOLRecordPDF )->GenPDF( $course_auth_id );
            $this->line( $res );
        }


        return 1;

    }


}
