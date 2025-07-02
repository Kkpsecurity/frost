import React, { useState } from "react";
import { Card, ListGroup, Button } from "react-bootstrap";

const ChallengeHistory = ({ data, colorSet }) => {
    const [showHistory, setShowHistory] = useState(false);

    const toggleHistory = () => {
        setShowHistory((prev) => !prev);
    };

    // Function to check if there are at least two consecutive incomplete challenges
    const hasTwoConsecutiveFailures = () => {
        let count = 0;
        const { allChallenges } = data;

        for (let i = 0; i < allChallenges.length; i++) {
            if (!allChallenges[i].completed_at) {
                count++;
                if (count >= 2) {
                    return true; // If there are two or more consecutive failures
                }
            } else {
                count = 0; // Reset count if there's a completed challenge
            }
        }

        return false; // No two consecutive failures
    };

    return (
        <Card>
            <ListGroup.Item>
                <Card.Title className="d-flex justify-content-between align-items-center p-0">
                    <h4 style={{ fontSize: "1.0rem" }}>Challenge History</h4>
                    <Button variant="primary" onClick={toggleHistory}>
                        {showHistory ? (
                            <i className="fa fa-arrow-up" />
                        ) : (
                            <i className="fa fa-arrow-down" />
                        )}
                    </Button>
                </Card.Title>

                {showHistory &&
                    (hasTwoConsecutiveFailures() ? (
                        <div className="text-danger">
                            You failed the lesson due to missing two challenges
                            in a row.
                        </div>
                    ) : (
                        <div>
                            {data.allChallenges.map((challenge) => (
                                <div
                                    key={challenge.id}
                                    className="d-flex justify-content-between align-items-center"
                                >
                                    <div>
                                        {new Date(
                                            challenge.created_at * 1000
                                        ).toLocaleString()}
                                    </div>
                                    <div>
                                        {challenge.completed_at ? (
                                            <i className="fa fa-check text-success"></i>
                                        ) : (
                                            <i className="fa fa-ban text-danger"></i>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    ))}
            </ListGroup.Item>
        </Card>
    );
};

export default ChallengeHistory;
