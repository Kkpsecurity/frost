<?php
namespace App\Http\Controllers\Admin\Services;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AdminChartServiceController extends Controller
{
    /**
     * Fetch data for the registration chart.
     *
     * @return \Illuminate\Http\Response
     */
    public function registration()
    {
        $specificMonth = date('m');
        $specificYear = date('Y');

        // Default array for months
        $defaultMonthlyData = array_fill_keys(range(1, 12), 0);

        // Default array for days in the current month
        $daysInMonth = date('t');
        $defaultDailyData = array_fill_keys(range(1, $daysInMonth), 0);

        $yearlyRegistrationsByMonth = DB::table('users')
            ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('count(*) as count'))
            ->whereYear('created_at', $specificYear)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('count', 'month')
            ->toArray();

        $completeYearlyData = $yearlyRegistrationsByMonth + $defaultMonthlyData;


        $monthlyRegistrationsByDay = DB::table('users')
            ->select(DB::raw('EXTRACT(DAY FROM created_at) as day'), DB::raw('count(*) as count'))
            ->whereYear('created_at', $specificYear)
            ->whereMonth('created_at', $specificMonth)
            ->groupBy(DB::raw('EXTRACT(DAY FROM created_at)'))
            ->pluck('count', 'day')
            ->toArray();

        $completeMonthlyData = $monthlyRegistrationsByDay + $defaultDailyData;

        return response()->json([
            'year' => $completeYearlyData,
            'month' => $completeMonthlyData
        ]);
    }



}