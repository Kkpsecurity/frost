<?php

namespace App\Http\Controllers\Web;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\RangeSelect;
use App\Models\CourseAuth;
use App\Models\RangeDate;
use App\Traits\PageMetaDataTrait;


class RangeDateController extends Controller
{

    use PageMetaDataTrait;


    public function Select( CourseAuth $CourseAuth )
    {

        $view = 'frontend.range_date.select';

        $content = self::renderPageMeta( $view );

        $UpcomingRangeDates = RangeSelect::UpcomingRangeDates();

        return view( $view, compact([ 'content', 'CourseAuth', 'UpcomingRangeDates' ]) );

    }


    public function Show( CourseAuth $CourseAuth )
    {

        // student selected no Range Date
        if ( $CourseAuth->range_date_id == -1 )
        {
            return redirect()->to( Auth::user()->Dashboard() );
        }


        $view = 'frontend.range_date.show';

        $content = self::renderPageMeta( $view );

        if ( ! $RangeDate = $CourseAuth->RangeDate )
        {
            return redirect()->route( 'range_date.select', $CourseAuth );
        }

        $Range = $RangeDate->Range;

        return view( $view, compact([ 'content', 'RangeDate', 'Range' ]) );

    }


    public function Update( CourseAuth $CourseAuth, RangeDate $RangeDate )
    {

        $CourseAuth->update([
            'range_date_id' => $RangeDate->id
        ]);

        // dispatch email

        if ( $RangeDate->id == -1 )
        {
            return redirect()->to( Auth::user()->Dashboard() );
        }

        return redirect()->route( 'range_date.show', $CourseAuth );

    }

}
