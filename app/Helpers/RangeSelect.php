<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

use App\Models\Range;
use App\Models\RangeDate;


class RangeSelect
{


    public static function UpcomingRangeDates() : Collection
    {

        $RangeDates = RangeDate::where( 'is_active', true )
                               ->where( 'id', '!=', -1 )  // no date selected
                               ->where( function ( $query ) {
                                        $query->where( 'start_date', '>', date( 'Y-m-d' ) )
                                            ->orWhere( 'appt_only', false );
                                      })
                             ->orderBy( 'start_date' )
                                ->with( 'Range' )
                                 ->get()
                               ->where( 'Range.is_active', true )
                              ->sortBy( 'Range.city' );


        $RangeDatesByRange = collect([]);

        foreach ( $RangeDates as $RangeDate )
        {

            if ( ! $RangeDatesByRange->has( $RangeDate->Range->id ) )
            {
                $RangeDatesByRange->put(
                    $RangeDate->Range->id,
                    collect([
                        'Range'      => $RangeDate->Range,
                        'RangeDates' => collect([]),
                    ])
                );
            }

            $RangeDatesByRange->get( $RangeDate->Range->id )
                              ->get( 'RangeDates' )
                             ->push( $RangeDate->withoutRelations() );

        }

        return $RangeDatesByRange;


    }


    public static function MakeSelectOpts( Collection $RangeDatesByRange, int|string $selected = '' ) : string
    {

        $opts = '';

        foreach ( $RangeDatesByRange as $Record )
        {

            $opts .= '<optgroup label="' .  e( $Record->get( 'Range' )->city ) . '">' . "\n";

            foreach ( $Record->get( 'RangeDates' ) as $RangeDate )
            {
                $opts .= "<option value=\"{$RangeDate->id}\""
                       . ( $selected == $RangeDate->id ? ' selected' : '' )
                       . '>' . e( $RangeDate->DateStr() ) . "</option>\n";
            }

        }

        return $opts;

    }


}
