import React from "react";
import {
    Card,
    Col,
    Container,
    ListGroup,
    ListGroupItem,
    Row,
} from "react-bootstrap";
import { ProfileType } from "../../../../Config/types";

interface Props {
    profile: ProfileType;
}

const Dashboard: React.FC<Props> = ({ profile }) => {

   
    const listGroupItems = [
        {
            title: "Avatar",
            content: <img src={profile.avatar} width="40" />,
        },
        {
            title: "Name",
            content: `${profile.fname} ${profile.lname}`,
        },
        {
            title: "Email",
            content: profile.email,
        },
        {
            title: "Account Created",
            content: profile.created_at,
        },
    ];

    return (
        <Container>
            <Row>
                <Col lg={12} style={{ marginBottom: "40px" }}>
                    <Card className="shadow">
                        <Card.Header>
                            <h3 className="text-dark">Personal Detail</h3>
                        </Card.Header>
                        <ListGroup>
                            {listGroupItems.map((item, index) => (
                                <ListGroupItem
                                    key={index}
                                    className="d-flex justify-content-between align-items-center"
                                >
                                    {item.title}:<span>{item.content}</span>
                                </ListGroupItem>
                            ))}
                        </ListGroup>
                    </Card>
                </Col>
            </Row>

            <Row>
                <Col lg={12}>
                    <Card>
                        <Card.Header>
                            <h3 className="text-dark">Contact Info</h3>
                        </Card.Header>
                        <ListGroup>
                            <ListGroupItem className="d-flex justify-content-between align-items-center">
                                Phone: <span>---</span>
                            </ListGroupItem>
                        </ListGroup>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default Dashboard;
