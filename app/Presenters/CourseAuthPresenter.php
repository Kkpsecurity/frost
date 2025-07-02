<?php

namespace App\Presenters;

use stdClass;
use Illuminate\Support\Carbon;

use App\Models\StudentUnit;


trait CourseAuthPresenter
{


    public function StartDate( string $fmt = 'ddd YYYY-MM-DD' ) : string
    {
        return Carbon::parse( $this->start_date )->isoFormat( $fmt );
    }


    public function StartedAt() : string
    {

        if ( $StudentUnit = $this->StudentUnits->first() )
        {
            return $StudentUnit->CreatedAt();
        }

        return '';

    }


    public function StatusObj() : stdClass
    {

        $status = (object) [
            'is_started' => false,
            'is_active'  => true,
            'is_passed'  => $this->is_passed,
            'html'       => '<i>Not Started</i>'
        ];


        if ( $this->start_date )
        {
            $status->is_started = true;
            $status->html       = '<i>Incomplete</i>';
        }

        if ( $this->completed_at )
        {
            $status->is_active = false;
            $status->html      = $this->is_passed ? 'Passed' : '<span style="color: red;">Failed</span>';
        }

        if ( $this->disabled_at )
        {
            $status->is_active = false;
            $status->html      = "<span style=\"color: red;\">{$this->disabled_reason}</span>";
        }


        return $status;

    }



    public function FinalStatus( bool $use_html = false ) : string
    {

        if ( $this->disabled_reason )
        {
            return $use_html ? "<span style=\"color: red; font-weight: bold;\">{$this->disabled_reason}</span>" : $this->disabled_reason;
        }

        if ( ! $this->start_date or ! $this->completed_at )
        {
            return '';
        }

        if ( $this->is_passed )
        {
            return 'Pass';
        }

        return $use_html ? '<span style="color: red;">Fail</span>' : 'Fail';

    }


}
