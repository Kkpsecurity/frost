<!-- Add a form for creating a new ticket -->
<form action="{{ url('tickets.store') }}" method="post">
    @csrf
    <div class="form-group">
        <label for="ticketSubject">Subject</label>
        <input type="text" class="form-control" id="ticketSubject" name="subject" required>
    </div>
    <div class="form-group">
        <label for="ticketDescription">Description</label>
        <textarea class="form-control" id="ticketDescription" name="description" rows="3" required></textarea>
    </div>
    <!-- You may add more fields based on your actual database structure -->
    <!-- For example, if you have a 'priority' field -->
    <div class="form-group">
        <label for="ticketPriority">Priority</label>
        <select class="form-control" id="ticketPriority" name="priority">
            <option value="High">High</option>
            <option value="Medium" selected>Medium</option>
            <option value="Low">Low</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit Ticket</button>
</form>
