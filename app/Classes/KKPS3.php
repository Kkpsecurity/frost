<?php

declare(strict_types=1);

namespace App\Classes;

use Exception;
use Aws\S3\S3Client;
use Aws\Result as AwsResult;
use Aws\S3\Exception\S3Exception;


use App\Traits\AssertConfigTrait;


class KKPS3
{

    use AssertConfigTrait;


    protected static $_S3Client;
    protected static $_config;
    protected static $_log_times = false;


    public static function boot(): self
    {

        if (is_object(self::$_config)) {
            // already booted
            return new self();
        }


        self::$_config = self::AssertConfig('kkps3', ['access_key', 'secret_key', 'endpoint', 'region', 'bucket']);


        //
        // initialize S3Client
        //

        if (self::$_log_times) $starttime = microtime(true);

        self::$_S3Client = new S3Client([

            'version'       => 'latest',
            'endpoint'      => self::$_config->endpoint,
            'region'        => self::$_config->region,
            'credentials'   => [
                'key'       => self::$_config->access_key,
                'secret'    => self::$_config->secret_key,
            ],
            'use_path_style_endpoint' => true

        ]);

        if (self::$_log_times) logger('KKPS3 connect:  ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return new self();
    }


    public static function S3Client(): S3Client
    {

        return self::boot()::$_S3Client;
    }


    public static function ListObjects(bool $only_contents = true): AwsResult|array
    {

        if (self::$_log_times) $starttime = microtime(true);

        $result = self::S3Client()->listObjectsV2([
            'Bucket' => config('kkps3.bucket'),
        ]);

        if (self::$_log_times) logger('KKPS3 listobjs: ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return $only_contents ? $result['Contents'] : $result;
    }


    public static function GetObject(string $key): AwsResult
    {

        if (self::$_log_times) $starttime = microtime(true);

        $result = self::S3Client()->getObject([
            'Bucket' => config('kkps3.bucket'),
            'Key'    => $key,
        ]);

        if (self::$_log_times) logger('KKPS3 getObj:   ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return $result;
    }



    public static function PutFile(string $abs_filename, string $key = null): AwsResult
    {

        if (! file_exists($abs_filename)) {
            throw new Exception("Not Found: ${$abs_filename}");
        }


        if (self::$_log_times) $starttime = microtime(true);

        $result = self::S3Client()->putObject([
            'Bucket'      => self::$_config->bucket,
            'Key'         => $key ?:  basename($abs_filename),
            'ContentType' => mime_content_type($abs_filename),
            'Body'        => file_get_contents($abs_filename),
        ]);

        if (self::$_log_times) logger('KKPS3 putFile:  ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return $result;
    }


    public static function PutObject(string $key, mixed $data, string $content_type = null): AwsResult
    {

        if (self::$_log_times) $starttime = microtime(true);

        $result = self::S3Client()->putObject([
            'Bucket'      => self::$_config->bucket,
            'Key'         => $key,
            'Body'        => $data,
            'ContentType' => $content_type,
        ]);

        if (self::$_log_times) logger('KKPS3 putObj:   ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return $result;
    }


    public static function DeleteObject(string $key): AwsResult
    {

        if (self::$_log_times) $starttime = microtime(true);

        $result = self::S3Client()->deleteObject([
            'Bucket' => self::$_config->bucket,
            'Key'    => $key,
        ]);

        if (self::$_log_times) logger('KKPS3 delete:   ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');


        return $result;
    }


    public static function GetSignedURL(string $key, string $expire): ?string
    {

        if (! self::ValidateKey($key)) {
            return null;
        }

        return (string) self::S3Client()
            ->createPresignedRequest(
                self::S3Client()->getCommand('GetObject', [
                    'Bucket' => self::$_config->bucket,
                    'Key'    => $key,
                ]),
                $expire
            )->getUri();

        /*
        $cmd = self::S3Client()->getCommand( 'GetObject', [
            'Bucket' => self::$_config->bucket,
            'Key'    => $key,
        ]);

        $result = self::S3Client()->createPresignedRequest( $cmd, $expire );

        return (string) $result->getUri();
        */
    }



    public static function ValidateKey(string $key): bool
    {

        try {

            if (self::$_log_times) $starttime = microtime(true);

            $result = self::S3Client()->headObject([
                'Bucket' => self::$_config->bucket,
                'Key'    => $key,
            ]);

            if (self::$_log_times) logger('KKPS3 validate: ' . sprintf('%0.3f', (microtime(true) - $starttime)) . 'ms');

            return true;
        } catch (S3Exception $e) {

            return false;
        }
    }
}
