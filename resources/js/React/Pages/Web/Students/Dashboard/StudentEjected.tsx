import React from "react";
import { BsExclamationTriangleFill } from "react-icons/bs"; // Import Bootstrap icon
import { Container, Row, Col } from "react-bootstrap";

const StudentEjected = ({ classData }) => {
    // Dummy data for the ban description
    const banDescription =
        "You have been Ejected from accessing the class. Please follow the rules to re instate you access.";

    return (
        <Container fluid className="bg-light">
            <Row className="mx-auto">
                <Col
                    xs={12}
                    className="vh-100 d-flex align-items-center justify-content-center "
                >
                    <div style={{
                        marginTop: "-10%",
                        textAlign: "center"
                    }}>
                        <BsExclamationTriangleFill size={50} color="red" />
                        <h2 className="mt-2">You have been ejected From Class!</h2>
                        <p className="mt-3">{banDescription}</p>
                    </div>
                </Col>
            </Row>
        </Container>
    );
};

export default StudentEjected;
