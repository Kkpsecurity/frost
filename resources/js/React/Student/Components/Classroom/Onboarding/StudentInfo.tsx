import React from "react";
import { useFormContext } from "react-hook-form";
import TextInput from "../../../../Shared/Components/FormFields/TextInput.tsx";
import Select from "../../../../Shared/Components/FormFields/Select.tsx";
import PhoneInput from "../../../../Shared/Components/FormFields/PhoneInput.tsx";
import DatePicker from "../../../../Shared/Components/FormFields/DatePicker.tsx";
import { t } from "@/i18n";

interface StudentInfoProps {
    student?: any;
}

const StudentInfo: React.FC<StudentInfoProps> = ({ student }) => {
    // Extract data from student_info JSON if available
    const studentInfo = student?.student_info || {};

    const suffixOptions = [
        { text: t('onboarding.suffixNone'), value: "" },
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
                        title={t('onboarding.firstName')}
                        value={student?.fname || studentInfo?.fname || ""}
                        required={true}
                    />
                </div>

                {/* Last Name */}
                <div className="col-md-6">
                    <TextInput
                        id="lname"
                        title={t('onboarding.lastName')}
                        value={student?.lname || studentInfo?.lname || ""}
                        required={true}
                    />
                </div>

                {/* Middle Initial */}
                <div className="col-md-6">
                    <TextInput
                        id="initial"
                        title={t('onboarding.middleInitial')}
                        value={student?.initial || studentInfo?.initial || ""}
                        required={false}
                    />
                </div>

                {/* Suffix */}
                <div className="col-md-6">
                    <Select
                        id="suffix"
                        title={t('onboarding.suffix')}
                        value={student?.suffix || studentInfo?.suffix || ""}
                        options={suffixOptions}
                        required={false}
                    />
                </div>

                {/* Phone */}
                <div className="col-md-6">
                    <PhoneInput
                        id="phone"
                        title={t('onboarding.phoneNumber')}
                        value={student?.phone || studentInfo?.phone || ""}
                        required={true}
                        mask="999-999-9999"
                    />
                </div>

                {/* Date of Birth */}
                <div className="col-md-6">
                    <DatePicker
                        id="dob"
                        title={t('onboarding.dateOfBirth')}
                        value={student?.dob || studentInfo?.dob || ""}
                        required={true}
                    />
                </div>
            </div>
        </div>
    );
};

export default StudentInfo;
