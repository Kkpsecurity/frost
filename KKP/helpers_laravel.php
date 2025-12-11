<?php


if ( ! function_exists( 'api_abort' ) )
{
    function api_abort( $code = 500, $message = '[Unspecified]' )
    {
        if ( request()->wantsJson() )
        {
            response()->json([ 'error' => $code, 'message' => $message ], $code )->throwResponse();
        }
        abort( $code, $message );
        #response( "({$code}) {$message}" )->throwResponse();
    }
}


if ( ! function_exists( 'abortToRoute' ) )
{
    function abortToRoute( string $route, string $message, string $type = 'error' )
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            redirect( $route )->with( $type, $message )
        );
    }
}


if ( ! function_exists( 'abortToDashboard' ) )
{
    function abortToDashboard( string $message, string $type = 'error' )
    {
        if ( ! auth()->user() or ! method_exists( auth()->user(), 'Dashboard' ) )
        {
            abort( 500, $message );
        }
        abortToRoute( auth()->user()->Dashboard(), $message, $type );
    }
}


if ( ! function_exists( 'nag' ) )
{
    function nag( string $message ) : void
    {
        if ( App::environment( 'production' ) )
        {
            logger( "Nag: {$message}" );
        }
        else
        {
            throw new Exception( $message );
        }
    }
}


if ( ! function_exists( 'CollectionToOpts' ) )
{
    function CollectionToOpts( Illuminate\Support\Collection $Collection, $pluck, bool $addblank = false ) : array
    {
        $opts = $Collection->pluck( ...(array)$pluck )->toArray();
        return ( $addblank ? [ '' => '' ] + $opts : $opts );
    }
}


if ( ! function_exists( 'DevTool' ) )
{
    function DevTool( string $action, ...$args )
    {
        if ( ! app()->environment( 'production' ) )
        {
            // never returns
            \App\DevTool\DevTool::handle( $action, ...$args );
        }
    }
}


if ( ! function_exists( 'IsQueueWorker' ) )
{
    function IsQueueWorker() : bool
    {
    	if ( App::runningInConsole() )
    	{
    		foreach ( Request::server( 'argv' ) as $arg )
            {
                if ( strpos( $arg, 'queue:' ) !== false )
                {
                    return true;
                }
            }
    	}
        return false;
    }
}


function dumpcap( $var, $disable_debug_bar = false )
{

    if ( $disable_debug_bar && class_exists( '\Debugbar' ) )
    {
        \Debugbar::disable();
    }

    ob_start();
    dump( $var );
    return ob_get_clean();

}
