<?php

namespace App\Http\Controllers\Frontend\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Range;

/**
 * RangesController
 *
 * Public-facing controller for displaying shooting ranges
 * with Google Maps integration
 */
class RangesController extends Controller
{
    /**
     * Display listing of active ranges with map
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get ALL ranges - exclude placeholder/test ranges
        $ranges = Range::with('RangeDates')
            ->where('price', '>', 0)
            ->where('name', '!=', 'No Range Date Selected')
            ->orderBy('city')
            ->orderBy('name')
            ->paginate(5);

        // Get unique cities for filter
        $cities = Range::select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        // Get Google Maps API key
        $googleMapsApiKey = config('services.google_maps.api_key');

        return view('frontend.ranges.index', compact('ranges', 'cities', 'googleMapsApiKey'));
    }
}
