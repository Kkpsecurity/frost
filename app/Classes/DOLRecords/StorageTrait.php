<?php
declare(strict_types=1);

namespace App\Classes\DOLRecords;

use KKP\Laravel\Traits\StoragePathTrait;


trait StorageTrait
{

    use StoragePathTrait;


    public function __construct()
    {

        $this->AssertStoragePath( 'D40' );
        $this->AssertStoragePath( 'G28' );

        $this->_fontpath = storage_path( $this->_fontpath );

    }


    public function StoragePath() : string
    {
        return storage_path( 'app/dolrecords');
    }


    protected function FileName() : string
    {

        $subdir = ( 1 == $this->_Course->id ? 'D40' : 'G28' );

        return $this->StoragePath()
             . "/{$subdir}/"
             . e( $this->_User->lname ) . ', ' . e( $this->_User->fname )
             . " ({$this->_CourseAuth->id}).pdf";

    }


}
