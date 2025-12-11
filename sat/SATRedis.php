<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Redis;
#use Illuminate\Support\Collection;

use App\RCache;



class SATRedis
{


    public function ShowKeys( int $cache_id = null )
    {


        $cache_keys = $this->CacheKeys();

        if ( $cache_id )
        {
            abort_unless( isset( $cache_keys[ $cache_id ] ), 404 );
        }
        else
        {
            $cache_id = array_keys( $cache_keys )[0];
        }



        #
        #
        #


        $html = $this->_Header( $cache_id );



        $Redis = $this->_Redis( $cache_id );

        $csrf_token   = csrf_token();
        $delkey_route = route( 'sattest.redis.delkey' );



        $keys = $Redis->keys( '*' );
        natsort( $keys );

        foreach ( $keys as $key )
        {

            $key_name = htmlspecialchars( $key );

            if ( 'hash' == $Redis->type( $key ) )
            {

                $value = 'hash';

            }
            else
            {

                $value = $Redis->get( $key );

                if ( preg_match( '/^a\:\d*\:\{.*\}$/', $value ) )
                {
                    $data = unserialize( $value );
                    $value = "<pre>\n" . print_r( $data, true ) . "</pre>";
                }
                else
                {
                    $value = htmlspecialchars( $Redis->get( $key ) );
                }

            }


            $html .=<<<ROW
<tr>
  <td valign="top">
    <form method="post" action="{$delkey_route}">
      <input type="hidden" name="_token"    value="{$csrf_token}" />
      <input type="hidden" name="cache_id"  value="{$cache_id}" />
      <input type="hidden" name="redis_key" value="{$key}" />
      <button type="submit">Delete</button>
    </form>
  </td>
  <td valign="top" nowrap>{$key_name}</td>
  <td valign="top">{$value}</td>
</tr>
ROW;
        }

        return $html . $this->_Footer();

    }


    public function DelKey()
    {

        $cache_id  = request()->input( 'cache_id' );
        $redis_key = request()->input( 'redis_key' );

        $this->_Redis( (int) $cache_id )->del( $redis_key );

        return redirect()->route( 'sattest.redis', $cache_id );

    }


    public function CacheKeys() : array
    {

        $laravel_db = Cache::store( 'redis' )->connection()->client()->getConnection()->getParameters()->database;
        $rcache_db  = RCache::Redis()->getConnection()->getParameters()->database;

        return [

            $laravel_db => [
                'route' => route( 'sattest.redis', $laravel_db ),
                'title' => "Laravel ({$laravel_db})",
                'conn'  => 'cache',
            ],

            $rcache_db => [
                'route' => route( 'sattest.redis', $rcache_db ),
                'title' => "RCache ({$rcache_db})",
                'conn'  => 'rcache',
            ],

        ];

    }


    //
    // Redis
    //


    private function _Redis( int $cache_id )
    {
        return Redis::Connection( $this->CacheKeys()[ $cache_id ][ 'conn' ] );
    }


    //
    // HTML
    //


    private function _Header( int $cache_id = null ) : string
    {

        $cache_opts = '';

        foreach ( $this->CacheKeys() as $id => $data )
        {
            $cache_opts .= "<option value=\"{$data['route']}\""
                . ( $id == $cache_id ? ' selected' : '' )
                . ">{$data['title']}</option>\n";
        }

        return str_replace( '|CACHEOPTS|', $cache_opts, file_get_contents( base_path( '/sat/redis_header.html' ) ) );

    }


    private function _Footer() : string
    {

        return file_get_contents( base_path( '/sat/redis_footer.html' ) );

    }


}
