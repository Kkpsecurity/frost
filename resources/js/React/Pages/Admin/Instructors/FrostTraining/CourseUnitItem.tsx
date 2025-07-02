import React, { useEffect, useState } from "react";
import { Card, ListGroup } from "react-bootstrap";
import { CourseType, LaravelAdminShape } from "../../../../Config/types";
import styled from "styled-components";

interface Props {
    course: CourseType;
    laravel: LaravelAdminShape;
    handleSetView: () => void;
    handleTakeOver: () => void;
    handleAssignAssistant: () => void;
    assistantId?: string;
    setAssignedAssistantId: () => void;
    debug?: boolean;
}


const StyledCard = styled(Card)`
    width: 20rem;
    background-color: #f5f5f5;

    .text-green {
        color: #28a745;
    }

    @media (max-width: 768px) {
        width: 15rem;
    }

    @media (max-width: 576px) {
        width: 100%;
    }
`;

const ArrowIcon = styled.i`
    cursor: pointer;
    color: white;
    display: flex;
    align-self: flex-end;
`;

const Details = styled.div`
    display: ${(props) => (props.show ? 'block' : 'none')};
    margin: 10px;
    background-color: #f5f5f5;
    padding: 10px;
    border-radius: 5px;
    transition: all 0.5s ease-in-out;

    @media (max-width: 576px) {
        margin: 5px;
        padding: 5px;
    }
`;


const CourseUnitItem: React.FC<Props> = ({
    course,
    laravel,
    handleSetView,
    handleTakeOver,
    handleAssignAssistant,
    assistantId,
    debug = false,
}) => {
    if (debug) console.log("CourseUnitItem: ", course);
    const [createdBy, setCreatedBy] = useState<string>("");
    const [assistantBy, setAssistantBy] = useState<string>("");
    const [showDetails, setShowDetails] = useState(false);

    console.log("COURSEUNITITEM: ", course);

    useEffect(() => {
        if (course?.createdBy?.length > 0) {
            setCreatedBy(course.createdBy);
        }

        if (course?.assistantBy?.length > 0) {
            setAssistantBy(course.assistantBy);
        }
    }, [course]);

    type ActionsBarProps = {
        handleTakeOver: (event: React.MouseEvent<HTMLButtonElement>) => void;
        handleAssist: (event: React.MouseEvent<HTMLButtonElement>) => void;
    };

    const ActionsBar: React.FC<ActionsBarProps> = ({
        handleTakeOver,
        handleAssist,
    }) => (
        <div className="d-flex align-items-center justify-content-center">
            <button
                id={String(course.id)}
                className="btn btn-primary mr-2"
                onClick={handleTakeOver}
            >
                Take Over
            </button>
            <button
                id={String(course.id)}
                className="btn btn-success mr-2"
                onClick={handleAssignAssistant}
            >
                Assist
            </button>
        </div>
    );

    const cardContent = [
        {
            label: "Instructor",
            value: createdBy || "OPEN",
        },
        {
            label: "Assistant",
            value: assistantBy || "OPEN",
        },
        {
            label: "Start Date",
            value: course.starts_at,
        },
        {
            label: "End Date",
            value: course.ends_at,
        },
    ];

    const detailsOptions = [
        {
            title: "Take Over",
            description:
                "You can take over the class from the current instructor. Note: Taking over the class does not require any approval methods; hence, you will kick out the instructor. Unless you are positive about this step, do not proceed.",
        },
        {
            title: "Assist",
            description:
                "You can assist the instructor. This will load a new view that will allow you to communicate and perform validation tasks.",
        },
    ];

    /**
     * This is the start the course and assign the instructor Card
     */
    return (
        <StyledCard>
            {createdBy.length > 0 ? (
                <>
                    <Card.Header className="bg-dark text-white d-flex justify-content-between align-items-center">
                        <h4
                            style={{
                                width: "90%",
                            }}
                        >
                            Class In Session
                        </h4>
                        <p
                            className="p-1 mt-1"
                            onClick={() => setShowDetails(!showDetails)}
                        >
                            <ArrowIcon className="fa fa-arrow-down"></ArrowIcon>
                        </p>
                    </Card.Header>

                    <Details show={showDetails}>
                        <>
                            <p>You have two options</p>
                            <ol>
                                {detailsOptions.map((option) => (
                                    <li key={option.title}>
                                        <strong>{option.title}:</strong>{" "}
                                        {option.description}
                                    </li>
                                ))}
                            </ol>
                        </>
                    </Details>
                </>
            ) : (
                <Card.Img
                    variant="top"
                    src="https://i0.wp.com/www.s2institute.com/wp-content/uploads/2022/03/AT201-DL-COURSEIMG-VECTOR-REP.jpg?fit=500%2C300&ssl=1"
                />
            )}

            <Card.Body>
                <Card.Title>
                    <b>{course.title}</b>
                </Card.Title>
            </Card.Body>

            <ListGroup className="list-group-flush">
                {cardContent.map((item) => (
                    <ListGroup.Item
                        key={item.label}
                        className="text-dark d-flex justify-content-between align-items-center"
                    >
                        {item.label}: <span>{item.value}</span>
                    </ListGroup.Item>
                ))}
            </ListGroup>

            <Card.Footer>
                {createdBy.length > 0 ? (
                    <ActionsBar
                        handleTakeOver={handleTakeOver}
                        handleAssist={handleAssignAssistant}
                    />
                ) : (
                    <Card.Link
                        href="#"
                        id={String(course.id)}
                        className="btn btn-success btn-sm float-right"
                        onClick={handleSetView}
                    >
                        Select Course
                    </Card.Link>
                )}
            </Card.Footer>
        </StyledCard>
    );
};

export default CourseUnitItem;
