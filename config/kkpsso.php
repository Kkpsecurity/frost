<?php
/*
 *  This is not used by this application
 *
 *  EXAMPLE:
 *
 *  $KKPSSO = new KKP\KKPSSO\Remote( define( 'kkpsso.config' ) );
 *  $KKPSSO->Validate( $username, $password, (bool) $as_string );
 *
 */

return [

    'config' => [
        'aws_secret_key_id'     => env('AWS_ACCESS_KEY_ID'),
        'aws_secret_access_key' => env('AWS_SECRET_ACCESS_KEY'),
        'aws_default_region'    => env('AWS_DEFAULT_REGION'),
        'aws_bucket'            => 'kkpsso',
        'aws_object_key'        => 'kkpsso.bin.enc',
        'local_file'           => storage_path('app/kkpsso/kkpsso.bin.enc'),
    ],

];
