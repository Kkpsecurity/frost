<?php

namespace App\Classes;

use KKP\Laravel\Traits\StoragePathTrait;

class ValidationsPhotos
{
    use StoragePathTrait;

    public static function StoragePath()
    {
        return 'app/public/validations';
    }

    public static function defaultPhoto()
    {
        $path = self::joinPath('no-image.png');
        return self::pathToUrl($path);
    }

    public static function getIdCard($course_auth_id)
    {
        if(is_null($course_auth_id)) return false;
        $path = self::joinPath('idcards', "{$course_auth_id}.png");
        return file_exists($path) ? self::pathToUrl($path) : self::defaultPhoto();
    }

    public static function getHeadshot($student_unit_id)
    {
        if(is_null($student_unit_id)) return false;
        $path = self::joinPath('headshots', "{$student_unit_id}.png");
       
        return file_exists($path) ? self::pathToUrl($path) : self::defaultPhoto();
    }

    protected static function joinPath(...$segments)
    {
        $path = join(DIRECTORY_SEPARATOR, array_merge([self::StoragePath()], $segments));
        return storage_path($path);
    }

    protected static function pathToUrl($path)
    {
        return vasset( $path, true );
    }
}
