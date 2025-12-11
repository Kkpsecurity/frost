<?php


function kkps3_test()
{


    $filename = '2021-07-20 09.03.54.jpg';
    $srcfile  = base_path( 'sat/' . $filename );


    /*
    $key = 'Test File.jpg';
    KKPS3::DeleteObject( $key );
    KKPS3::PutObject( $key, file_get_contents( $srcfile ), mime_content_type( $srcfile ) );
    $object = KKPS3::GetObject( $key );
    */


    KKPS3::DeleteObject( $filename );
    KKPS3::PutFile( $srcfile );


    /*
    if ( $url = KKPS3::GetSignedURL( $filename, '+20 minutes' ) )
        return "<a href=\"{$url}\" target=\"_blank\">{$url}</a>\n";
    else
        return 'Bad Key';
    */



    $object = KKPS3::GetObject( $filename );
    header( "Content-Type:   {$object['ContentType']}"   );
    header( "Content-Length: {$object['ContentLength']}" );
    print $object['Body'];
    exit();


}
