import React, { useState } from "react";
import axios from "axios";
import { useForm, FormProvider } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import StudentAgreementText from "./StudentAgreementText";
import StudentInfo from "./StudentInfo";
import CheckBox from "../../../../Shared/Components/FormFields/CheckBox.tsx";

interface StudentAgreementProps {
    student: any;
    course: any;
    onSuccess: () => void;
}

interface FormData {
    fname: string;
    lname: string;
    initial?: string;
    suffix?: string;
    phone: string;
    dob: string;
    agreement: boolean;
}

// Validation schema
const schema = yup.object().shape({
    agreement: yup.boolean().oneOf([true], "Agreement must be checked"),
    dob: yup
        .date()
        .required("Date of birth is required")
        .max(
            new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            "You must be at least 18 years old"
        ),
    fname: yup.string().required("First name is required"),
    lname: yup.string().required("Last name is required"),
    phone: yup
        .string()
        .matches(
            /^\d{3}-\d{3}-\d{4}$/,
            "Phone number must be in the format 999-999-9999"
        )
        .required("Phone number is required"),
});

/**
 * StudentAgreement - Terms and Conditions acceptance form with student info
 *
 * Full form from archived component with:
 * - Personal info fields: fname, lname, initial, suffix, phone, DOB
 * - Yup validation with age check (18+) and phone format
 * - React Hook Form integration
 * - API: /student/onboarding/accept-terms
 */
const StudentAgreement: React.FC<StudentAgreementProps> = ({
    student,
    course,
    onSuccess,
}) => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [submitted, setSubmitted] = useState(false);

    // Extract student_info if available
    const studentInfo = student?.student_info || {};

    // Parse name field if fname/lname not available
    const parseName = (fullName: string) => {
        if (!fullName) return { fname: "", lname: "" };
        const parts = fullName.trim().split(" ");
        if (parts.length === 1) return { fname: parts[0], lname: "" };
        const fname = parts[0];
        const lname = parts.slice(1).join(" ");
        return { fname, lname };
    };

    const { fname: parsedFname, lname: parsedLname } = parseName(student?.name || "");

    // Debug: log student data
    console.log('=== StudentAgreement Component ===');
    console.log('Student data:', student);
    console.log('Student info:', studentInfo);
    console.log('Parsed name:', { parsedFname, parsedLname });
    console.log('Form defaultValues:', {
        fname: student?.fname || studentInfo?.fname || parsedFname || "",
        lname: student?.lname || studentInfo?.lname || parsedLname || "",
        initial: student?.initial || studentInfo?.initial || "",
        suffix: student?.suffix || studentInfo?.suffix || "",
        phone: student?.phone || studentInfo?.phone || "",
        dob: student?.dob || studentInfo?.dob || "",
    });

    const methods = useForm<FormData>({
        resolver: yupResolver(schema),
        defaultValues: {
            fname: student?.fname || studentInfo?.fname || parsedFname || "",
            lname: student?.lname || studentInfo?.lname || parsedLname || "",
            initial: student?.initial || studentInfo?.initial || "",
            suffix: student?.suffix || studentInfo?.suffix || "",
            phone: student?.phone || studentInfo?.phone || "",
            dob: student?.dob || studentInfo?.dob || "",
            agreement: false,
        },
    });

    const onSubmit = async (data: FormData) => {
        setLoading(true);
        setError(null);

        try {
            // Format DOB
            const formattedDOB = data.dob
                ? new Date(data.dob).toISOString().split("T")[0]
                : "";

            const response = await axios.post("/classroom/portal/student/agreement", {
                course_date_id: student.course_date_id,
                agreement: true,
                dob: formattedDOB,
                fname: data.fname,
                initial: data.initial,
                lname: data.lname,
                suffix: data.suffix,
                phone: data.phone,
                student_id: student.id,
                course_auth_id: student.course_auth_id,
            });

            if (response.data.success) {
                setSubmitted(true);
                // Show success message briefly, then call onSuccess
                setTimeout(() => {
                    onSuccess();
                }, 1500);
            }
        } catch (err: any) {
            setError(
                err.response?.data?.message ||
                    "Failed to submit agreement. Please try again."
            );
        } finally {
            setLoading(false);
        }
    };

    // Show success state
    if (submitted) {
        return (
            <div className="text-center" style={{ padding: "3rem", marginTop: "10px" }}>
                <i
                    className="fas fa-check-circle"
                    style={{ fontSize: "4rem", color: "#2ecc71", marginBottom: "1.5rem" }}
                ></i>
                <h5 style={{ color: "white", marginBottom: "1rem" }}>
                    Agreement Accepted!
                </h5>
                <p style={{ color: "#95a5a6" }}>
                    Preparing your classroom seat...
                </p>
                <div className="spinner-border text-primary mt-3" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <div style={{ marginTop: "1rem" }}>
            <FormProvider {...methods}>
                <form onSubmit={methods.handleSubmit(onSubmit)}>
                    {/* Course Header */}
                    <div className="text-center mb-3" style={{
                        backgroundColor: "#34495e",
                        padding: "1rem",
                        borderRadius: "0.5rem"
                    }}>
                    <i
                        className="fas fa-graduation-cap"
                        style={{ fontSize: "2rem", color: "#3498db", marginBottom: "0.5rem" }}
                    />
                    <h5 style={{ color: "white", marginBottom: "0.25rem" }}>
                        {course?.name || "Course Registration"}
                    </h5>
                    <p style={{ color: "#95a5a6", fontSize: "0.875rem", marginBottom: 0 }}>
                        Please complete your information and accept the terms below
                    </p>
                </div>

                {/* Student Info Component */}
                <StudentInfo student={student} />

                {/* Terms Display */}
                <StudentAgreementText />

                {/* Error Alert */}
                {error && (
                    <div
                        className="alert alert-danger"
                        style={{
                            backgroundColor: "rgba(231, 76, 60, 0.1)",
                            border: "1px solid #e74c3c",
                            color: "#e74c3c",
                            marginTop: "1rem",
                            marginBottom: "1rem",
                        }}
                    >
                        <i className="fas fa-exclamation-triangle me-2"></i>
                        {error}
                    </div>
                )}

                {/* Agreement Checkbox */}
                <div
                    style={{
                        backgroundColor: "#34495e",
                        padding: "1rem",
                        borderRadius: "0.5rem",
                        marginTop: "1.5rem",
                        marginBottom: "1.5rem",
                    }}
                >
                    <CheckBox
                        id="agreement"
                        title="I have read and agree to the Student Terms and Conditions"
                        required={true}
                    />
                </div>

                {/* Submit Button */}
                <button
                    type="submit"
                    className="btn btn-primary w-100"
                    disabled={loading}
                    style={{
                        fontSize: "1.1rem",
                        padding: "0.75rem",
                        fontWeight: "bold",
                    }}
                >
                    {loading ? (
                        <>
                            <i className="fas fa-spinner fa-spin me-2"></i>
                            Processing...
                        </>
                    ) : (
                        <>
                            <i className="fas fa-check me-2"></i>
                            Accept & Continue
                        </>
                    )}
                </button>
            </form>
        </FormProvider>
        </div>
    );
};

export default StudentAgreement;
