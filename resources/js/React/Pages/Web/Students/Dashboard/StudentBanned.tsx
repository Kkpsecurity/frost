import React from "react";
import { BsExclamationTriangleFill } from "react-icons/bs"; // Import Bootstrap icon
import { Container, Row, Col } from "react-bootstrap";

const StudentBanned = () => {
    // Updated message for the banned description
    const banDescription =
        "You have been banned from accessing this class. To regain access, you must repurchase the course. Please ensure you follow the course rules to avoid future bans.";

    return (
        <Container fluid className="bg-light">
            <Row className="mx-auto">
                <Col
                    xs={12}
                    className="vh-100 d-flex align-items-center justify-content-center"
                >
                    <div style={{ marginTop: "-10%", textAlign: "center" }}>
                        <BsExclamationTriangleFill size={50} color="red" />
                        <h2 className="mt-2">Access Denied</h2>
                        <p className="mt-3">{banDescription}</p>
                    </div>
                </Col>
            </Row>
        </Container>
    );
};

export default StudentBanned;
