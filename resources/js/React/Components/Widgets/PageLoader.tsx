import React from "react";
import Spinner from "react-bootstrap/Spinner";
import Container from "react-bootstrap/Container";

const PageLoader = () => {
    return (
        <Container
            className="d-flex justify-content-center align-items-center"
            style={{ height: "16rem" }}
        >
            <Spinner animation="border" variant="primary" role="status">
                <span className="visually-hidden">
                    Loading dashboard data...
                </span>
            </Spinner>
        </Container>
    );
};

export default PageLoader;
