<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;


use RCache;
use App\RCache\RCacheModelTrait;

use App\Models\Course;
use App\Models\Order;
#use App\Presenters\DiscountCodePresenter;
use App\Presenters\PresentsTimeStamps;
use App\Traits\ExpirationTrait;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\Observable;
use KKP\Laravel\ModelTraits\PgTimestamps;
use KKP\TextTk;


class DiscountCode extends Model
{

    #use DiscountCodePresenter;
    use ExpirationTrait, PgTimestamps, PresentsTimeStamps;
    use NoString, Observable, RCacheModelTrait;


    protected $table        = 'discount_codes';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',

        'code'              => 'string',  // 32

        'created_at'        => 'timestamp',
        'expires_at'        => 'timestamp',

        'course_id'         => 'integer',
        'set_price'         => 'decimal:2',
        'percent'           => 'integer',
        'max_count'         => 'integer',

        'client'            => 'string',  // 32
        'uuid'              => 'string',

    ];

    protected $guarded      = [ 'id' ];


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo( Course::class, 'course_id' );
    }

    public function Orders()
    {
        return $this->hasMany( Order::class, 'discount_code_id' );
    }


    //
    // incoming data filters
    //


    public function setCodeAttribute( $value )
    {
        $this->attributes[ 'code' ] = TextTk::Sanitize( $value );
    }

    public function setExpiresAtAttribute( $value )
    {
        //
        // convert raw date to EST
        //
        if ( $value ) $this->attributes[ 'expires_at' ] = Carbon::parse( $value, 'America/New_York' )->tz( 'UTC' );
    }

    public function setClientAttribute( $value )
    {
        $this->attributes[ 'client' ] = TextTk::Sanitize( $value );
    }


    //
    // cache queries
    //


    public function GetCourse() : ?Course
    {
        return ( $this->course_id ? RCache::Courses( $this->course_id ) : null );
    }


    //
    // helpers
    //


    public function AppliesFree() : bool
    {
        return ( $this->set_price === '0.00' );
    }


    public function TimesUsed() : int
    {
        return Order::where( 'discount_code_id', $this->id )
             ->whereNotNull( 'completed_at' )
                    ->count();
    }


}
