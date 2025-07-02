import React, { useEffect, useState, useContext } from "react";
import { Alert, Card, Form } from "react-bootstrap";
import {
    FormProvider,
    useForm,
    useController,
    SubmitHandler,
} from "react-hook-form";

import TextHidden from "../../../../../Components/FormElements/TextHidden";
import Password from "../../../../../Components/FormElements/Password";
import { CourseMeetingShape } from "../../../../../Config/types";

interface Props {
    courseDateId: number;
    data: CourseMeetingShape;
    setCredentials: (credentials: any) => void;
}

const AddZoomPassCode: React.FC<Props> = ({
    courseDateId,
    data,
    setCredentials,
}) => {
    // Update the zoom payload with the new credentials
  
    const methods = useForm();   

    return (
        <>
            <Card
                style={{
                    width: "100%",
                    background: "#111",
                    marginTop: "10px",
                }}
            >
                <Card.Header >
                    <b>Zoom Credentials</b>
                </Card.Header>
                <Card.Body>
                    {data?.instructor && data?.instructor?.zoom_payload.zoom_passcode ? (
                        <Alert variant="success">
                            <h5 className="text-dark">
                                Zoom Credentials have been set for this meeting.
                            </h5>
                        </Alert>
                    ) : (
                        <FormProvider {...methods}>
                            <Form
                                onSubmit={methods.handleSubmit(
                                    setCredentials
                                )}
                            >
                                <TextHidden
                                    id="meetingID"
                                    value={data.instructor.zoom_payload.pmi}
                                />                               

                                <Password
                                    id="passCode"
                                    title="Zoom PassCode"
                                    required={true}
                                />

                                <button
                                    type="submit"
                                    className="btn btn-success float-right"
                                >
                                    Update Credentials
                                </button>
                            </Form>
                        </FormProvider>
                    )}
                </Card.Body>
            </Card>
        </>
    );
};

export default AddZoomPassCode;
