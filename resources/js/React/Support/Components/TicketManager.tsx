import React, { useState } from 'react';

interface TicketManagerProps {
    tickets?: any[];
}

const TicketManager: React.FC<TicketManagerProps> = ({ tickets = [] }) => {
    const [activeTickets, setActiveTickets] = useState(tickets);

    return (
        <div className="ticket-manager">
            <div className="card">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h6>Ticket Manager</h6>
                    <button className="btn btn-sm btn-success">
                        New Ticket
                    </button>
                </div>
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {activeTickets.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="text-center">
                                            No tickets available
                                        </td>
                                    </tr>
                                ) : (
                                    activeTickets.map((ticket, index) => (
                                        <tr key={index}>
                                            <td>{ticket.id}</td>
                                            <td>{ticket.student}</td>
                                            <td>{ticket.subject}</td>
                                            <td>
                                                <span className={`badge bg-${ticket.status === 'open' ? 'danger' : 'success'}`}>
                                                    {ticket.status}
                                                </span>
                                            </td>
                                            <td>
                                                <button className="btn btn-sm btn-outline-primary">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TicketManager;
