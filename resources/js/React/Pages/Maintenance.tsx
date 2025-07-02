import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';

const Maintenance = () => {
  return (
    <div className="bg-light text-white" style={{ minHeight: "100vh" }}>
      <Container className="py-5">
        <Row>
          <Col md={12} className="text-center">
            <h1 className="display-4">Site Maintenance</h1>
            <hr className="bg-white" />
            <p className="lead">Our system is currently undergoing maintenance to ensure the highest level of security for our users.</p>
            <p className="lead">We apologize for any inconvenience this may cause and appreciate your patience.</p>
          </Col>
        </Row>
      </Container>
    </div>
  )
}

export default Maintenance;
