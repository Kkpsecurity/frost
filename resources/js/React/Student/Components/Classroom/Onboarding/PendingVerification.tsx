import React from "react";
import { Card, Container, ToastContainer } from "react-bootstrap";
import styled from "styled-components";
import { ClassDataShape, StudentType } from "../../../../../../Config/types";
import CaptureIDForValidation from "../../Video/CaptureIDForValidation";


import {
    StyledCard,
    CardHeader,
    CardBody,
    CaptureContainer,
} from "../../../../../../Styles/StylePendingVerfication.styled";

interface Props {
    data: ClassDataShape | null;
    student: StudentType;
    validations: {
        headshot: string | string[] | null;
        idcard: string | null;
        message: string | null;
    } | null;
    debug?: boolean;
}

const PendingVerification: React.FC<Props> = ({
    data,
    student,
    validations,
    debug = false,
}) => {
    return (
        <CaptureContainer fluid>
            <StyledCard>
                <CardHeader>
                    <h3>Student ID Verification:</h3>
                    <span>
                        <b>
                            {student.fname} {student.lname}
                        </b>
                    </span>
                </CardHeader>
                <CardBody>
                    <CaptureIDForValidation
                        data={data}
                        student={student}
                        validations={validations}
                        debug={debug}
                    />
                </CardBody>
            </StyledCard>
        </CaptureContainer>
    );
};

export default PendingVerification;
