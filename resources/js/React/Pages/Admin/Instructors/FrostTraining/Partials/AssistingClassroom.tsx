import React from "react";
import apiClient from "../../../../../Config/axios";

const AssistingClassroom = ({ assistantId }) => {
    const [assistant, setAssistant] = React.useState(null);
    const [error, setError] = React.useState(null);

    React.useEffect(() => {
        apiClient
            .get(`/admin/instructors/assistants/${assistantId}`)
            .then((response) => {
                setAssistant(response.data);
            })
            .catch((error) => {
                setError(error.response.data.message);
            });
    }, []);

    if (assistant === null) {
        return <></>;
    }

    return (
        <div className="p-3">
            <h5 className="text-dark">Assistant</h5>
            <img
                src={assistant.avatar}
                alt="student-profile"
                width="40"
                className="profile-photo"
            /> {" "}
            <span className="text-dark">               
                {assistant.fname} {assistant.lname}
            </span>
        </div>
    );
};

export default AssistingClassroom;
