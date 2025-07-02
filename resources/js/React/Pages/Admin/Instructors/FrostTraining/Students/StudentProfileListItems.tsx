import React, { useEffect, useState } from "react";
import { ListGroup } from "react-bootstrap";
import { formatPhone } from "../../../../../Helpers/helpers";

const StudentProfileListItems = ({ selectedStudent, handleImageClick }) => {
    const [todaysHeadshot, setTodaysHeadshot] = useState("");

    const ImageStyles = {
        width: "40px",
        height: "40px",
        margin: 0,
        cursor: "pointer",
    };

    useEffect(() => {
        if (!selectedStudent || !selectedStudent.validations) {
            console.log("Validations are not available.");
            return;
        }
        
        if (selectedStudent.validations.headshot) {
            setTodaysHeadshot(selectedStudent.validations.headshot);
        }

    }, [selectedStudent]);

    
    useEffect(() => {
        const getStartOfDay = (date: Date) => {
            date.setHours(0, 0, 0, 0);
            return date;
        };

        const today = getStartOfDay(new Date());

        if (selectedStudent.validations && selectedStudent.validations.headshot) {
            const headshots = selectedStudent.validations.headshot;
            Object.keys(headshots).forEach((key) => {
                const dateOfHeadshot = getStartOfDay(new Date(parseInt(key)));
                if (dateOfHeadshot.getTime() === today.getTime()) {
                    const todayHeadshots = headshots[key];
                    setTodaysHeadshot(Array.isArray(todayHeadshots) ? todayHeadshots[0] : todayHeadshots);
                }
            });
        }

    }, [selectedStudent.validations]);

    if (!selectedStudent) {
        return <div>Student information is not available.</div>;
    }
   
    const idCardImage = selectedStudent.validations.idcard;
    const bgClass = idCardImage.includes("no-image") ? "bg-danger" : "bg-gray";
    const headbgClass = todaysHeadshot.includes("no-image") ? "bg-danger" : "bg-gray";

    if(!idCardImage || !todaysHeadshot) {
        return null;
    }


    return (
        <>
            {["fname", "initial", "lname", "suffix", "email", "phone"].map(
                (field) => (
                    <ListGroup.Item
                        key={field}
                        className="bg-light d-flex justify-content-between"
                    >
                        <span>
                            {field.charAt(0).toUpperCase() +
                                field.slice(1).replace("_", " ")}
                            :
                        </span>
                        <span>
                            {field === "phone"
                                ? formatPhone(
                                      selectedStudent?.student_info?.[field]
                                  )
                                : selectedStudent?.[field]}
                        </span>
                    </ListGroup.Item>
                )
            )}

            <ListGroup.Item className={bgClass + " d-flex justify-content-between"}>
                <span>ID Card:</span>
                <span>
                    <img
                        src={idCardImage}
                        alt="ID Card"
                        style={ImageStyles}
                        onClick={() => handleImageClick(idCardImage)}
                    />
                </span>
            </ListGroup.Item>

            <ListGroup.Item
                className={headbgClass + " d-flex justify-content-between"}
            >
                <span>Headshot:</span>
                <span>
                    <img
                        src={todaysHeadshot || ""}
                        alt="Head Shot"
                        style={ImageStyles}
                        onClick={() => handleImageClick(todaysHeadshot)}
                    />
                </span>
            </ListGroup.Item>
        </>
    );
};

export default StudentProfileListItems;
