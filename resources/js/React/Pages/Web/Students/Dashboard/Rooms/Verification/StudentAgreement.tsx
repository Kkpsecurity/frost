import React, { useContext, useState } from "react";
import { Alert, Button, Form } from "react-bootstrap";
import apiClient from "../../../../../../Config/axios";
import Loader from "../../../../../../Components/Widgets/Loader";

import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { format } from 'date-fns';

import TextInput from "../../../../../../Components/FormElements/TextInput";
import DatePicker from "../../../../../../Components/FormElements/DatePicker";
import { FormProvider, useForm } from "react-hook-form";
import CheckBox from "../../../../../../Components/FormElements/CheckBox";
import TextHidden from "../../../../../../Components/FormElements/TextHidden";
import Select from "../../../../../../Components/FormElements/Select";
import StudentAgreementText from "./StudentAgreementText";

import { StudentType, CourseType } from "../../../../../../Config/types";
import PhoneInput from "../../../../../../Components/FormElements/PhoneInput";
const schema = yup.object().shape({
    agreement: yup.boolean().oneOf([true], "Agreement must be checked"),
    dob: yup.date()
         .required("Date of birth is required")
         .max(new Date(new Date().setFullYear(new Date().getFullYear() - 18)), "You must be at least 18 years old"),
    fname: yup.string().required("First name is required"),
    lname: yup.string().required("Last name is required"),
    phone: yup.string()
    .matches(/^\d{3}-\d{3}-\d{4}$/, "Phone number must be in the format 999-999-9999")
    .required('Phone number is required'),
});


const StudentAgreement = ({
    student,
    course,
}: {
    student: StudentType;
    course: CourseType | null;
}) => {
    const [submitted, setSubmitted] = useState(false);

    const methods = useForm({
        resolver: yupResolver(schema),
    });

    const { watch } = methods;
    const agreed = watch("agreement", false);

    const onSubmit = async (data: {
        agreement: boolean;
        dob: string;
        fname: string;
        initial: string;
        lname: string;
        suffix: string;
        phone: string;
        student_id: number;
        course_auth_id: number;
    }) => {
        console.log("PostStudentAgreement: ", data);
        let formattedDOB = format(new Date(data.dob), 'MM/dd/yyyy');

        console.log("Formatted Phone: ", data.phone);
    
        try {
            const response = await apiClient.post("/classroom/portal/student/agreement", {
                agreement: true,
                dob: formattedDOB,
                fname: data.fname,
                initial: data.initial,
                lname: data.lname,
                suffix: data.suffix,
                phone: data.phone,
                student_id: data.student_id,
                course_auth_id: data?.course_auth_id,
            });
    
            // Check the response for success status
            if (response.data.success) {
                // Handle successful agreement update
                setSubmitted(true);
            } else {
                // Handle scenario where API responded but did not succeed (e.g., validation error)
                // This is just a precautionary measure; usually, a failed request will throw an error caught by the catch block
                alert(`Failed to update agreement: ${response.data.message}`);
            }
        } catch (error) {
            // Handle errors from the API request
            console.error("Error updating agreement: ", error.response?.data?.message || error.message);
            alert(`Error updating agreement: ${error.response?.data?.message || "An unknown error occurred."}`);
            // Keep the user on the form for correction or retries
            setSubmitted(false);
            // Optionally, manipulate the form's state or display error messages next to the relevant fields
        }
    };
    

    const suffixArray = [
        { value: "", text: "Select an Option" }, // 0
        { value: "Jr.", text: "Jr." }, // 1
        { value: "Sr.", text: "Sr." }, // 2
        { value: "I", text: "I" }, // 3
        { value: "II", text: "II" }, // 4
        { value: "III", text: "III" }, // 5
    ];

    const styles = {
        container: {
            padding: "3rem",
        },
        header: {
            color: "#000",
        },
        alert: {
            color: "#000",
        },
        successMessage: {
            color: "#000",
            fontSize: "1.8rem",
            fontWeight: "bold",
        },
        button: {
            color: "#fff",
            fontWeight: "bold",
        },
    };

    return (
        <div className="frost-secondary-bg" style={styles.container}>
            <style>
                {`
                label {
                    color: #000;
                }

                .form-control, select, .custom-select {
                    background-color: #eee;
                    color: #333;
                    margin-bottom: 10px;
                }

                .form-control:focus, select:focus, .custom-select:focus {
                    background-color: #fff;
                    color: #333;
                }

                select option {
                    background-color: #eee;
                    color: #333;
                    padding: 5px;
                }

                .form-check-input {
                    background-color: #eee;
                    color: #333;
                }
            `}
            </style>
            <div className="row">
                <div className="col-lg-12">
                    <div className="container">
                        <h2 style={styles.header}>Student Agreement</h2>
                        <Alert variant="success" style={styles.alert}>
                            Please read terms and conditions carefully before
                            proceeding.
                        </Alert>
                        <StudentAgreementText />

                        {!submitted ? (
                            <FormProvider {...methods}>
                                <Form onSubmit={methods.handleSubmit(onSubmit)} className="bg-light p-3">
                                    <TextHidden
                                        id="student_id"
                                        value={student.id}
                                    />
                                    <TextHidden
                                        id="course_auth_id"
                                        value={student.course_auth_id}
                                    />

                                    <TextInput
                                        id="fname"
                                        title="Full (Legal) First Name"
                                        required={true}
                                        value={student.fname}
                                    />

                                    <TextInput
                                        id="initial"
                                        title="Middle Initial"
                                        required={false}
                                        value=""
                                    />

                                    <TextInput
                                        id="lname"
                                        title="Full (Legal) Last Name"
                                        required={true}
                                        value={student.lname}
                                    />

                                    <Select
                                        id="suffix"
                                        title="Suffix"
                                        options={suffixArray}
                                        required={false}
                                        value=""
                                    />

                                    <PhoneInput
                                        id="phone"
                                        title="Enter a valid Contact Phone/Moblie Number"
                                        required={true}
                                        value=""
                                    />                                   

                                    <DatePicker
                                        id="dob"
                                        title="Date of Birth"
                                        required={true}
                                        value=""
                                    />

                                    <Form.Group
                                        controlId="agree"
                                        className="p-2 mb-2"
                                    >
                                        <CheckBox
                                            id="agreement"
                                            title="Agree"
                                            required={true}
                                        />
                                    </Form.Group>

                                    <Button
                                        variant="primary"
                                        type="submit"
                                        disabled={!agreed}
                                        style={{
                                            ...styles.button,
                                            backgroundColor: agreed
                                                ? "#007bff"
                                                : "#ccc",
                                        }}
                                       className="btn btn-primary btn-lg btn-block"
                                    >
                                        I Agree
                                    </Button>
                                </Form>
                            </FormProvider>
                        ) : (
                            <>
                                <div
                                    className="alert alert-success"
                                    style={styles.successMessage}
                                >
                                    Preparing your classroom seat...
                                </div>
                                <Loader />
                            </>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentAgreement;


