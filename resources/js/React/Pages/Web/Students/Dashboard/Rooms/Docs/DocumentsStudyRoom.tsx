import React from "react";
import { ClassDataShape, StudentType } from "../../../../../../Config/types";
import { Container, Row, Col, Card, ListGroup, Alert } from "react-bootstrap";

interface Props {
    data: ClassDataShape;
    student: StudentType;
    debug: boolean;
}

const DocumentsStudyRoom: React.FC<Props> = ({
    data,
    student,
    debug = false,
}) => {
    const documents = data?.documents;

    console.log(data.documents);
    // return (
    //     <Alert variant="warning">
    //         <Alert.Heading>Study Room Down for Maintainece</Alert.Heading>
    //         <p>
    //             Welcome to the study room! Here you can find a list of
    //             recommended reading materials to help you prepare for your
    //             course.
    //         </p>
    //         <hr />
    //     </Alert>
    // );

    const x = 0;
    return (
        <Container className="mt-5">
            <Row>
                <Col lg={12}>
                    <Card
                        style={{
                            height: "800px",
                            overflow: "auto",
                        }}
                    >
                        <Card.Header>
                            <h3>Course Documents</h3>
                        </Card.Header>
                        <Card.Body
                            style={{
                                padding: "0",
                                margin: "0",
                                height: "800px",
                                overflow: "auto",
                            }}
                        >
                            <ListGroup>
                                {Object.entries(documents).map(
                                    ([title, url], index) => (
                                        <ListGroup.Item key={index}>
                                            <a
                                                href={url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                <i className="fa fa-chevron-right" />{" "}
                                                {title}
                                            </a>
                                        </ListGroup.Item>
                                    )
                                )}
                            </ListGroup>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default DocumentsStudyRoom;
