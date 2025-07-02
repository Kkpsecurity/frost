import React from "react";
import { Card } from "react-bootstrap";
import { colors } from "../../../../Config/colors";

const LessonDocumentsCard = ({ data, darkMode }) => {
    const { documents } = data;

    const colorSet = darkMode ? colors.dark : colors.light;

    if (data.course.title == "Florida D40 (Dy)") {
        return (
            <Card className={`list-group-item list-group-item-action mb-2 p-0`}>
                <Card.Header
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                        color: colorSet.navbarTextColor,
                    }}
                >
                    Course Documents
                </Card.Header>
                <Card.Body style={{
                    color: colorSet.navbarTextColor,
                }}>
                    <div className="alert alert-danger">
                        <a href="">No Required Docuemnts</a>
                    </div>
                </Card.Body>
            </Card>
        );
    } else if (data.course.title == "Florida G28 (Dy)") {
        return (
            <Card className={`list-group-item list-group-item-action mb-2 p-0`}>
                <Card.Header
                    style={{
                        backgroundColor: "var(--frost-primary-bg)",
                    }}
                >
                    Course Documents
                </Card.Header>
                <Card.Body>
                    <div>
                        <a href="">
                            <i className="fa fa-file-pdf-o"></i> G Manual
                        </a>
                    </div>
                </Card.Body>
            </Card>
        );
    }

    return <></>;
};

export default LessonDocumentsCard;
