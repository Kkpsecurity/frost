<?php

namespace App\Models;

/**
 * @file RangeDate.php
 * @brief Model for range_dates table.
 * @details This model represents a date range for a specific range, including attributes like start date,
 * end date, price, and whether the range is active or requires an appointment.
 */

use Illuminate\Database\Eloquent\Model;

use App\Models\Range;
use App\Models\CourseAuth;

use App\Helpers\TextTk;
use App\Traits\NoString;
use App\Traits\TogglesBooleans;


class RangeDate extends Model
{

    use NoString;
    use TogglesBooleans;


    protected $table        = 'range_dates';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'range_id'          => 'integer',
        'is_active'         => 'boolean',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'price'             => 'decimal:2',
        'times'             => 'string',  // 64
        'appt_only'         => 'boolean',

    ];

    protected $guarded      = ['id'];

    protected $attributes   = [

        'is_active'         => true,
        'appt_only'         => false,

    ];


    //
    // relationships
    //


    public function CourseAuths()
    {
        return $this->hasMany(CourseAuth::class, 'range_date_id');
    }

    public function Range()
    {
        return $this->belongsTo(Range::class, 'range_id');
    }


    //
    // incoming data filters
    //


    public function setTimesAttribute($value)
    {
        $this->attributes['times'] = TextTk::Sanitize($value);
    }



    //
    // date formatters
    //


    public function StartDate(string $fmt = 'YYYY-MM-DD'): string
    {
        return $this->start_date->isoFormat($fmt);
    }

    public function EndDate(string $fmt = 'YYYY-MM-DD'): string
    {
        return $this->end_date ? $this->end_date->isoFormat($fmt) : '';
    }

    public function DateStr(string $fmt = 'ddd MMM DD YYYY'): string
    {

        if ($this->appt_only) {
            return 'By Appointment Only';
        }

        return $this->end_date
            ? "{$this->StartDate($fmt)} - {$this->EndDate($fmt)}"
            : $this->StartDate($fmt);
    }
}
