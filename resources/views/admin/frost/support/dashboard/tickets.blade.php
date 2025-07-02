
<?php
$tickets = []; // Placeholder for ticket data

$totalOpenTickets = 10; // Replace with your actual count
$totalCompletedTickets = 5; // Replace with your actual count

$totalInProgressTickets = 10; // Replace with your actual count
$totalPendingResponseTickets = 5; // Replace with your actual count
$openTicketsProgress = ($totalOpenTickets / ($totalOpenTickets + $totalInProgressTickets + $totalPendingResponseTickets + $totalCompletedTickets)) * 100;

$inProgressTicketsProgress = ($totalInProgressTickets / ($totalOpenTickets + $totalInProgressTickets + $totalPendingResponseTickets + $totalCompletedTickets)) * 100;
$pendingResponseTicketsProgress = ($totalPendingResponseTickets / ($totalOpenTickets + $totalInProgressTickets + $totalPendingResponseTickets + $totalCompletedTickets)) * 100;
$completedTicketsProgress = ($totalCompletedTickets / ($totalOpenTickets + $totalInProgressTickets + $totalPendingResponseTickets + $totalCompletedTickets)) * 100;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Support Dashboard</h5>

                <!-- Toolbar for creating a ticket -->
                <div class="card-tools">
                    <a href="{{ route('admin.frost-support.dashboard') }}" class="btn btn-primary">View Tickets</a>
                    <a href="{{ route('admin.frost-support.dashboard.create_ticket') }}" class="btn btn-success">Create Ticket</a>
                </div>
            </div>

            <div class="card-body">
                @if(request()->segment(4) == 'create_ticket')
                    @include('admin.frost.support.dashboard.create_ticket')
                @else
                    @include('admin.frost.support.dashboard.tickets_list')
                @endif
            </div>
        </div>
    </div>
</div>
