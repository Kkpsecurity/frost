<?php
declare(strict_types=1);

namespace App\Classes;

use Illuminate\Support\Carbon;

use App\Models\StudentLesson;
use KKP\Laravel\Traits\StoragePathTrait;


class PollingLog
{

    use StoragePathTrait;


    protected $StudentLesson;
    protected $log_filename;


    public function __construct( StudentLesson $StudentLesson )
    {

        $this->AssertStoragePath();

        $this->StudentLesson = $StudentLesson;
        $this->log_filename  = $this->StoragePath() . DIRECTORY_SEPARATOR . "{$StudentLesson->id}.txt";

    }


    public static function StoragePath()
    {
        return storage_path( 'app/polling');
    }


    public function Save() : void
    {

        $timestamp = Carbon::now()->tz( 'America/New_York' )
                                  ->isoFormat( 'YYYY-MM-DD HH:mm:ssZ' );

        $fh = fopen( $this->log_filename, 'a+' );
        fwrite( $fh, $timestamp . "\n" );
        fclose( $fh );

    }


    public function GetLog() : ?array
    {

        if ( ! file_exists( $this->log_filename ) )
        {
            return null;
        }


        $records = [];

        foreach ( file( $this->log_filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line )
        {
            $records[] = Carbon::parse( $line );
        }

        return $records;

    }


}
