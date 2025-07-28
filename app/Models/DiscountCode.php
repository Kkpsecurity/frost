<?php

namespace App\Models;

/**
 * @file DiscountCode.php
 * @brief Model for discount_codes table.
 * @details This model represents discount codes, including attributes like code, expiration, and associated courses.
 * It provides methods for managing discount codes and retrieving related data.
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;

use App\Models\Order;
use App\Models\Course;

use App\Helpers\TextTk;
use App\Traits\NoString;
use App\Traits\Observable;
use App\Traits\PgTimestamps;
use App\Traits\ExpirationTrait;
use App\Traits\RCacheModelTrait;
use App\Presenters\PresentsTimeStamps;
#use App\Presenters\DiscountCodePresenter;


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

    protected $guarded      = ['id'];


    //
    // relationships
    //


    public function Course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function Orders()
    {
        return $this->hasMany(Order::class, 'discount_code_id');
    }


    //
    // incoming data filters
    //


    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = TextTk::Sanitize($value);
    }

    public function setExpiresAtAttribute($value)
    {
        //
        // convert raw date to EST
        //
        if ($value) $this->attributes['expires_at'] = Carbon::parse($value, 'America/New_York')->tz('UTC');
    }

    public function setClientAttribute($value)
    {
        $this->attributes['client'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetCourse(): ?Course
    {
        return ($this->course_id ? RCache::Courses($this->course_id) : null);
    }


    //
    // helpers
    //


    public function AppliesFree(): bool
    {
        return ($this->set_price === '0.00');
    }


    public function TimesUsed(): int
    {
        return Order::where('discount_code_id', $this->id)
            ->whereNotNull('completed_at')
            ->count();
    }
}
