import React from "react";
import { useFormContext } from "react-hook-form";
import TextInput from "../../../../Shared/Components/FormFields/TextInput.tsx";
import Select from "../../../../Shared/Components/FormFields/Select.tsx";
import PhoneInput from "../../../../Shared/Components/FormFields/PhoneInput.tsx";
import DatePicker from "../../../../Shared/Components/FormFields/DatePicker.tsx";

interface StudentInfoProps {
    student?: any;
}

const StudentInfo: React.FC<StudentInfoProps> = ({ student }) => {
    // Extract data from student_info JSON if available
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

    // Debug logs
    console.log('=== StudentInfo Component ===');
    console.log('Student prop:', student);
    console.log('Student info extracted:', studentInfo);
    console.log('Parsed name:', { parsedFname, parsedLname });
    console.log('fname sources:', {
        direct: student?.fname,
        fromStudentInfo: studentInfo?.fname,
        fromParsedName: parsedFname,
        final: student?.fname || studentInfo?.fname || parsedFname || ""
    });
    console.log('lname sources:', {
        direct: student?.lname,
        fromStudentInfo: studentInfo?.lname,
        fromParsedName: parsedLname,
        final: student?.lname || studentInfo?.lname || parsedLname || ""
    });

    const suffixOptions = [
        { text: "None", value: "" },
        { text: "Jr", value: "Jr" },
        { text: "Sr", value: "Sr" },
        { text: "II", value: "II" },
        { text: "III", value: "III" },
        { text: "IV", value: "IV" },
    ];

    return (
        <div
            style={{
                backgroundColor: "#34495e",
                padding: "1rem",
                borderRadius: "0.5rem",
                marginBottom: "1rem",
            }}
        >
            {/* Form Fields */}
            <div className="row">
                {/* First Name */}
                <div className="col-md-6">
                    <TextInput
                        id="fname"
                        title="First Name"
                        value={student?.fname || studentInfo?.fname || parsedFname || ""}
                        required={true}
                    />
                </div>

                {/* Last Name */}
                <div className="col-md-6">
                    <TextInput
                        id="lname"
                        title="Last Name"
                        value={student?.lname || studentInfo?.lname || parsedLname || ""}
                        required={true}
                    />
                </div>

                {/* Middle Initial */}
                <div className="col-md-6">
                    <TextInput
                        id="initial"
                        title="Middle Initial"
                        value={student?.initial || studentInfo?.initial || ""}
                        required={false}
                    />
                </div>

                {/* Suffix */}
                <div className="col-md-6">
                    <Select
                        id="suffix"
                        title="Suffix"
                        value={student?.suffix || studentInfo?.suffix || ""}
                        options={suffixOptions}
                        required={false}
                    />
                </div>

                {/* Phone */}
                <div className="col-md-6">
                    <PhoneInput
                        id="phone"
                        title="Phone Number"
                        value={student?.phone || studentInfo?.phone || ""}
                        required={true}
                        mask="999-999-9999"
                    />
                </div>

                {/* Date of Birth */}
                <div className="col-md-6">
                    <DatePicker
                        id="dob"
                        title="Date of Birth"
                        value={student?.dob || studentInfo?.dob || ""}
                        required={true}
                    />
                </div>
            </div>
        </div>
    );
};

export default StudentInfo;
