<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Models\CourseAuth;
use App\Models\StudentUnit;
use KKP\Laravel\ModelTraits\NoString;
use KKP\TextTk;


class Validation extends Model
{

    const PATH_PRE = 'validations';
    const FILE_EXT = '.png';

    use NoString;


    protected $table        = 'validations';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;


    protected $casts        = [

        'id'                => 'integer',
        'uuid'              => 'string',
        'course_auth_id'    => 'integer',
        'student_unit_id'   => 'integer',
        'status'            => 'integer',  // [ -1, 0, 1 ]
        'id_type'           => 'string',   // 64
        'reject_reason'     => 'string',   // text

    ];

    protected $guarded      = [ 'id', 'uuid' ];

    protected $attributes   = [ 'status' => 0 ];


    //
    // relationships
    //


    public function CourseAuth()
    {
        return $this->belongsTo( CourseAuth::class, 'course_auth_id' );
    }

    public function StudentUnit()
    {
        return $this->belongsTo( StudentUnit::class, 'student_unit_id' );
    }


    //
    // incoming data filters
    //


    public function setIdTypeAttribute( $value )
    {
        $this->attributes[ 'id_type' ] = TextTk::Sanitize( $value );
    }

    public function setRejectReasonAttribute( $value )
    {
        $this->attributes[ 'reject_reason' ] = TextTk::Sanitize( $value );
    }


    //
    // helpers
    //


    public function IsChecked() : bool
    {
        return $this->status != 0;
    }


    public function IsValid() : bool
    {
        return $this->status > 0;
    }


    public function IsRejected() : bool
    {
        return $this->status < 0;
    }


    public function Accept( string $id_type = null ) : void
    {

        if ( $this->course_auth_id && ! $id_type )
        {
            throw new \Exception( 'ID Cards require an id_type' );
        }

        $this->update([
            'status'        => 1,
            'id_type'       => $id_type,
            'reject_reason' => null,
        ]);

    }


    public function Reject( string $reject_reason ) : void
    {

        $this->update([
            'status'        => -1,
            'id_type'       => null,
            'reject_reason' => $reject_reason,
        ]);

    }


    //
    // pathing
    //


    public function RelPath() : string
    {
        return self::PATH_PRE
             . ( $this->course_auth_id ? '/idcards/' : '/headshots/' )
             . $this->uuid . self::FILE_EXT;
    }


    public function AbsPath() : string
    {
        return storage_path( 'app/public/' . $this->RelPath() );
    }


    public function URL( bool $return_blank_img = false ) : ?string
    {

        return file_exists( $this->AbsPath() )
                ? url( 'storage/' . $this->RelPath() )
                : ( $return_blank_img ? url( 'storage/' . self::PATH_PRE . '/no-image.jpg' ) : null );

    }


}
