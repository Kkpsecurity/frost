import React from "react";
import { Alert } from "react-bootstrap";
import { ClassDataShape } from "../../../../../../Config/types";

interface Props {
    data: ClassDataShape;
    debug: boolean;
}

const DNCStudent = ({ data, debug = false }) => {
    const { course, instructor } = data;
    
    const message = `You have been DNC (Did Not Complete) in ${course.title}. 
        Please contact your instructor for more information.`;

    return (
        <Alert variant="danger">
            {message}
        </Alert>
    );
};

export default DNCStudent;
