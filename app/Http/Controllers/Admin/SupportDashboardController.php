<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Support Dashboard Controller
 * Handles the support dashboard functionality in the admin panel
 */
class SupportDashboardController extends Controller
{
    /**
     * Display the support dashboard
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // You can add data fetching logic here
        // For now, we'll pass some basic data
        $data = [
            'pageTitle' => 'Support Dashboard',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Support Dashboard', 'url' => null]
            ]
        ];

        return view('admin.support.dashboard', $data);
    }

    /**
     * Get support statistics (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        // Mock data - replace with actual database queries
        $stats = [
            'openTickets' => 24,
            'resolvedToday' => 18,
            'avgResponseTime' => '2.5 hrs',
            'pendingEscalation' => 3
        ];

        return response()->json($stats);
    }

    /**
     * Get recent tickets (API endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentTickets(Request $request)
    {
        // Mock data - replace with actual database queries
        $tickets = [
            [
                'id' => 1,
                'subject' => 'Unable to access course materials',
                'student' => 'John Smith',
                'priority' => 'high',
                'status' => 'open',
                'created' => '2 hours ago'
            ],
            [
                'id' => 2,
                'subject' => 'Video playback issues',
                'student' => 'Sarah Johnson',
                'priority' => 'medium',
                'status' => 'in-progress',
                'created' => '4 hours ago'
            ],
            [
                'id' => 3,
                'subject' => 'Assignment submission problem',
                'student' => 'Mike Davis',
                'priority' => 'urgent',
                'status' => 'open',
                'created' => '1 hour ago'
            ],
            [
                'id' => 4,
                'subject' => 'Account login difficulties',
                'student' => 'Emma Wilson',
                'priority' => 'low',
                'status' => 'resolved',
                'created' => '6 hours ago'
            ]
        ];

        return response()->json($tickets);
    }
}
