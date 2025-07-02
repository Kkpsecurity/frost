import React from "react";
import { ProfileDataShape } from "../../../../Config/types";

interface Props {
    profile: ProfileDataShape;
}
const BillingDetails: React.FC<Props> = ({ profile }) => {
    const paymentHistoryExists =
        profile.paymentHistory && profile.paymentHistory.length > 0;

    return (
        <div className="billing-dashboard">
            <h2 className="text-dark">Billing Dashboard</h2>
            <div className="billing-info">
                <ul className="list-group">
                    <li className="list-group-item d-flex justify-content-between">
                        Name:
                        <span>
                            {profile.user.fname} {profile.user.lname}
                        </span>
                    </li>
                    <li className="list-group-item d-flex justify-content-between">
                        Email: <span>{profile.user.email}</span>
                    </li>
                </ul>
            </div>
            {paymentHistoryExists && (
                <div className="payment-history">
                    <h3>Payment History</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {profile.paymentHistory.map((payment) => (
                                <tr key={payment.id}>
                                    <td>{payment.created_at}</td>
                                    <td>{payment.amount}</td>
                                    <td>{payment.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
            {!paymentHistoryExists && (
                <p className="alert alert-danger">
                    There are no payments to display.
                </p>
            )}
        </div>
    );
};

export default BillingDetails;
