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
        // Sample stats for the dashboard
        $stats = [
            'open_tickets' => 8,
            'resolved_today' => 12,
            'pending_review' => 5,
            'urgent_tickets' => 2
        ];

        // Sample active tickets
        $activeTickets = [
            [
                'id' => '001',
                'student_name' => 'John Doe',
                'student_email' => 'john@example.com',
                'subject' => 'Login Issues',
                'course' => 'Security Fundamentals',
                'priority' => 'high',
                'created_at' => '2 hours ago',
                'status' => 'open'
            ],
            [
                'id' => '002',
                'student_name' => 'Jane Smith',
                'student_email' => 'jane@example.com',
                'subject' => 'Video Playback Problems',
                'course' => 'Advanced Security',
                'priority' => 'medium',
                'created_at' => '4 hours ago',
                'status' => 'in_progress'
            ],
            [
                'id' => '003',
                'student_name' => 'Mike Johnson',
                'student_email' => 'mike@example.com',
                'subject' => 'Certificate Download',
                'course' => 'Cyber Defense',
                'priority' => 'low',
                'created_at' => '1 day ago',
                'status' => 'open'
            ]
        ];

        // Sample recent activity
        $recentActivity = [
            [
                'title' => 'Ticket resolved',
                'description' => 'Login issue fixed for Sarah Wilson',
                'time' => '30 minutes ago',
                'icon' => 'fa-check-circle',
                'color' => 'success'
            ],
            [
                'title' => 'New ticket created',
                'description' => 'Payment inquiry from Robert Davis',
                'time' => '1 hour ago',
                'icon' => 'fa-plus-circle',
                'color' => 'info'
            ],
            [
                'title' => 'Escalation requested',
                'description' => 'Technical issue escalated to Level 2',
                'time' => '2 hours ago',
                'icon' => 'fa-arrow-up',
                'color' => 'warning'
            ]
        ];

        // Sample metrics
        $metrics = [
            'avg_response' => '15 min',
            'resolution_rate' => '92%',
            'satisfaction' => '4.8/5'
        ];

        return view('dashboards.support.index', compact(
            'stats',
            'activeTickets',
            'recentActivity',
            'metrics'
        ));
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
            'openTickets' => 8,
            'resolvedToday' => 12,
            'avgResponseTime' => '15 min',
            'pendingReview' => 5,
            'urgentTickets' => 2,
            'satisfactionScore' => 4.8
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
                'id' => '001',
                'subject' => 'Unable to access course materials',
                'student' => 'John Smith',
                'email' => 'john@example.com',
                'priority' => 'high',
                'status' => 'open',
                'created' => '2 hours ago',
                'course' => 'Security Fundamentals'
            ],
            [
                'id' => '002',
                'subject' => 'Video playback issues',
                'student' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'priority' => 'medium',
                'status' => 'in_progress',
                'created' => '4 hours ago',
                'course' => 'Advanced Security'
            ],
            [
                'id' => '003',
                'subject' => 'Assignment submission problem',
                'student' => 'Mike Davis',
                'email' => 'mike@example.com',
                'priority' => 'urgent',
                'status' => 'open',
                'created' => '1 hour ago',
                'course' => 'Cyber Defense'
            ],
            [
                'id' => '004',
                'subject' => 'Account login difficulties',
                'student' => 'Emma Wilson',
                'email' => 'emma@example.com',
                'priority' => 'low',
                'status' => 'resolved',
                'created' => '6 hours ago',
                'course' => 'Security Fundamentals'
            ]
        ];

        return response()->json($tickets);
    }

    /**
     * Search for students
     */
    public function searchStudents(Request $request)
    {
        $query = $request->get('query');
        $course = $request->get('course');

        // Mock search results - replace with actual database query
        $students = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'course' => 'Security Fundamentals',
                'status' => 'Active',
                'last_login' => '2 hours ago'
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'course' => 'Advanced Security',
                'status' => 'Active',
                'last_login' => '1 day ago'
            ]
        ];

        return response()->json([
            'success' => true,
            'students' => $students,
            'query' => $query
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateTicket(Request $request, $ticketId)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent'
        ]);

        // In a real app, update the ticket in the database

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'ticket_id' => $ticketId
        ]);
    }

    /**
     * Create new ticket
     */
    public function createTicket(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        // In a real app, create the ticket in the database

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket_id' => rand(1000, 9999)
        ]);
    }

    /**
     * Generate support report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:daily,weekly,monthly',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        // In a real app, generate the actual report

        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully',
            'download_url' => '/admin/support/reports/' . rand(1000, 9999) . '.pdf'
        ]);
    }
}
