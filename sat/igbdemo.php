<?php


$Users     = App\Models\User::all();
$users_arr = []; foreach ( $Users as $User ) { array_push( $users_arr, $User->toArray() ); }

$Users_php = serialize( $Users );
$Users_igb = igbinary_serialize( $Users );
$u_arr_php = serialize( $users_arr );
$u_arr_igb = igbinary_serialize( $users_arr );


print "<pre style=\"font-size: 15px\">\n"
    . "Collection of Users\n"
    . "-------------------\n"
    . 'PHP  '  . strlen( $Users_php ) . "\n"
    . 'IGB   ' . strlen( $Users_igb ) . '  ' . sprintf( '%0.2f', ( strlen( $Users_igb ) / strlen( $Users_php ) * 100 ) ) . "%\n"
    . "\n\n"
    . "array of Users properties\n"
    . "-------------------------\n"
    . 'PHP   ' . strlen( $u_arr_php ) . "\n"
    . 'IGB   ' . strlen( $u_arr_igb ) . '  ' . sprintf( '%0.2f', ( strlen( $u_arr_igb ) / strlen( $u_arr_php ) * 100 ) ) . "%\n"
    . "</pre>\n";

exit();
