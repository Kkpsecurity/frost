<?php


function SAT_Header() : string
{
    return file_get_contents( base_path( 'sat/_sat_header.html' ) );
}


function SAT_Footer() : string
{
    return JSData::ToHTML() . "\n"
          . file_get_contents( base_path( 'sat/_sat_footer.html' ) );
}
