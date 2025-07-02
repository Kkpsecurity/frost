<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\RangeDate;
use KKP\Laravel\ModelTraits\TogglesBooleans;
use KKP\TextTk;


class Range extends Model
{

    use TogglesBooleans;


    protected $table        = 'ranges';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'is_active'         => 'boolean',

        'name'              => 'string',  // 255
        'city'              => 'string',  // 255
        'address'           => 'string',  // 255

        'inst_name'         => 'string',  // 255
        'inst_email'        => 'string',  // 255
        'inst_phone'        => 'string',  // 16

        'price'             => 'decimal:2',
        'times'             => 'string',  // 64
        'appt_only'         => 'boolean',

        'range_html'        => 'string',  // text

    ];

    protected $guarded      = [ 'id' ];

    protected $attributes   = [ 'is_active' => true ];


    public function __toString() { return $this->name; }


    //
    // relationships
    //


    public function RangeDates()
    {
        return $this->hasMany( RangeDate::class, 'range_id' );
    }


    //
    // incoming data filters
    //


    public function setNameAttribute( $value )
    {
        $this->attributes[ 'name' ] = TextTk::Sanitize( $value );
    }

    public function setCityAttribute( $value )
    {
        $this->attributes[ 'city' ] = TextTk::Sanitize( $value );
    }

    public function setAddressAttribute( $value )
    {
        $this->attributes[ 'address' ] = TextTk::Sanitize( $value );
    }

    public function setInstNameAttribute( $value )
    {
        $this->attributes[ 'inst_name' ] = TextTk::Sanitize( $value );
    }

    public function setInstEmailAttribute( $value )
    {
        $this->attributes[ 'inst_email' ] = TextTk::Sanitize( $value );
    }

    public function setInstPhoneAttribute( $value )
    {
        $this->attributes[ 'inst_phone' ] = TextTk::Sanitize( $value );
    }

    public function setTimesAttribute( $value )
    {
        $this->attributes[ 'times' ] = TextTk::Sanitize( $value );
    }

    #public function setRangeHtmlAttribute( $value )
    #{
    #    $this->attributes[ 'range_html' ] = TextTk::Sanitize( $value, TextTk::SANITIZE_NO_STRIPTAGS );
    #}


}
