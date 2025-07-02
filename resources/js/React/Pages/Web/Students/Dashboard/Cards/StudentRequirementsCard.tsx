import React, { useEffect, useState } from "react";
import { Card, ListGroup } from "react-bootstrap";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons';
import { ClassDataShape, LaravelDataShape } from "../../../../../Config/types";
import { colors } from "../../../../../Config/colors";
import { format } from "date-fns";
import { date } from "yup";

type RequirementCardProps = {
    darkMode: boolean;
    data: ClassDataShape;
    laravel: LaravelDataShape;
    debug: boolean;
};

function StudentRequirementCard({ darkMode, data, laravel, debug }: RequirementCardProps) {
    const { validations } = laravel.user;

    // Retrieve the agreement statuses from local storage
    const studentAgreement = validations.authAgreement;    
    

    // Get today's date in 'YYYY-MM-DD' format
    const today = format(new Date(), 'yyyy-MM-dd');

    // Retrieve the class rules agreement date from local storage
    const agreedDate = localStorage.getItem("agreedToRules");
    const studentAgreedToClassRules = agreedDate === today;
    const colorSet = darkMode ? colors.dark : colors.light;

    const [TodaysHeadshot, setTodaysHeadshot] = useState<string>("");

    useEffect(() => {
        try {
            if (validations?.headshot) {
                const headshotKeys = Object.keys(validations.headshot);
                const todaysHeadshot = headshotKeys.find((key) => key.includes(today));
                if (todaysHeadshot) {
                    setTodaysHeadshot(validations.headshot[todaysHeadshot]);
                } else {
                    setTodaysHeadshot(""); // Handle case where no headshot is found for today
                }
            }
        } catch (error) {
            console.error("Error retrieving headshot data:", error);
            // Optionally set a fallback or error state here
        }
    }, [validations, today]);
    


    return (
        <Card className={`list-group-item list-group-item-action mb-2 p-0`}>
            <Card.Header
                style={{
                    backgroundColor: colorSet.navbarBgColor,
                    color: colorSet.navbarTextColor,
                }}
            >
                Validation Requirements
            </Card.Header>
            <ListGroup variant="flush">
                <ListGroup.Item
                    className="d-flex justify-content-between align-items-center"
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                        border: "1px solid" + colorSet.navbarBgColor,
                    }}
                >
                    <strong>Headshot:</strong>
                    <img src={TodaysHeadshot} alt="Headshot" width={40} />
                </ListGroup.Item>
                <ListGroup.Item
                    className="d-flex justify-content-between align-items-center"
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                        border: "1px solid" + colorSet.navbarBgColor,
                    }}
                >
                    <strong>ID Card:</strong>
                    <img src={validations?.idcard} alt="ID Card" width={40} />
                </ListGroup.Item>
                <ListGroup.Item
                    className="d-flex justify-content-between align-items-center"
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                        border: "1px solid" + colorSet.navbarBgColor,
                    }}
                >
                    <strong>Student Agreement:</strong>
                    {studentAgreement ? <FontAwesomeIcon icon={faCheckCircle} color="green" /> : "Pending"}
                </ListGroup.Item>
                <ListGroup.Item
                    className="d-flex justify-content-between align-items-center"
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                        border: "1px solid" + colorSet.navbarBgColor,
                    }}
                >
                 <strong>Class Rules Agreement for {today}:</strong>
                {studentAgreedToClassRules 
                    ? <FontAwesomeIcon icon={faCheckCircle} color="green" />
                    : "Pending"}</ListGroup.Item>
            </ListGroup>
        </Card>
    );
}

export default StudentRequirementCard;
