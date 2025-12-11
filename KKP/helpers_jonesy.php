<?php


/**
 * Recursively convert hash to stdClass
 *
 * @param   mixed      $param
 * @return  \stdClass
 */
function arrayToObject( $param )
{

    // https://www.if-not-true-then-false.com/2009/php-tip-convert-stdclass-object-to-multidimensional-array-and-convert-multidimensional-array-to-stdclass-object/

    if ( is_array( $param ) )
    {
        return (object) array_map( __FUNCTION__, $param );
    }

    return $param;

}


/**
 * Determine if array is simple( indexed )
 *
 * @param   array  $arr
 * @return  bool
 */
function is_simple( array &$arr )
{

    $idx = 0;

    foreach( $arr as $key => $val )
    {
        if ( $key !== $idx ) return false;
        $idx++;
    }

    return true;

}


/**
 * Convert indexed array to kvp
 *
 * @param   array  $arr
 * @return  array
 */
function idx2hash( array &$arr )
{

    if ( ! is_simple( $arr ) )
    {
        throw new \Exception( __FUNCTION__ . '() requires indexed array' );
    }

    return array_combine( $arr, $arr );

}


function no_cache_headers()
{

    header( 'Expires: on, 01 Jan 1970 00:00:00 GMT' );
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s \G\M\T' ) );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false);
    header( 'Pragma: no-cache' );

}


/**
 * Return "<class>::<method>" without namespace
 *
 * @return  string
 */
function ShortMethodName() : string
{

    $caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1];

    return ( substr( $caller['class'], strrpos( $caller['class'], '\\' ) + 1 ) )
            . '::'
            . $caller['function'];

}



/**
 * print_r array with <pre> tags
 *
 * @param   array        $arr
 * @param   bool         $return
 * @return  string|null  (html)
 */
function printArr( array $arr, $return = false ) : ?string
{
    $html = "<pre>\n" . print_r( $arr, true ) . "</pre>\n";
    if ( $return ) return $html; print $html; return null;
}


/**
 * Emulate coalescing operator
 *
 * @param   mixed  $a
 * @param   mixed  $b
 * @param   bool   $log
 * @return  bool
 */
function retern( $a, $b, $log = false ) : bool
{

    if ( ! isset($a)  ) { if ( $log ) error_log( 'retern: ! isset' ); return $b; }
    if ( is_null($a)  ) { if ( $log ) error_log( 'retern: is_null' ); return $b; }
    if ( empty($a)    ) { if ( $log ) error_log( 'retern: empty'   ); return $b; }
    if ( $a === false ) { if ( $log ) error_log( 'retern: false'   ); return $b; }
    if ( $a === 0     ) { if ( $log ) error_log( 'retern: zero'    ); return $b; }
    if ( $a === '0'   ) { if ( $log ) error_log( "retern: '0'"     ); return $b; }
    return $a;

}


/**
 * Lorem Ipsum
 *
 * @return  string
 */
function lorem( int $ver = null ) : string
{

	switch( $ver )
	{

		case 3:
			return str_replace( "\n", ' ', <<<LOREM
At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis
praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias
excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui
officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum
quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta
nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat
facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus
autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut
et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum
hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias
consequatur aut perferendis doloribus asperiores repellat.
LOREM
			);
			break;

		case 2:
			return str_replace( "\n", ' ', <<<LOREM
Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium
doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore
veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim
ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia
consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque
porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis
nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex
ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea
voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem
eum fugiat quo voluptas nulla pariatur?
LOREM
			);
			break;

		default:
			return str_replace( "\n", ' ', <<<LOREM
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis
nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
culpa qui officia deserunt mollit anim id est laborum.
LOREM
			);

	}

}
