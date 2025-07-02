import React, { useEffect, useState } from "react";
import { FormProvider, useForm } from "react-hook-form";
import DatePicker from "../../../../Components/FormElements/DatePicker";
import TextInput from "../../../../Components/FormElements/TextInput";
import Select from "../../../../Components/FormElements/Select";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import apiClient from "../../../../Config/axios";
import Loader from "../../../../Components/Widgets/Loader";
import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { ToastContainer } from "react-bootstrap";

const profileEndpoint = "/admin/frost-support/student-profile/update";

const ProfileEdit = ({ classData, student }) => {
    // Common suffixes
    const suffixes = [
        "Select Suffix",
        "Jr.",
        "Sr.",
        "I",
        "II",
        "III",
        "IV",
        "V",
    ];

    // Define default structure for student_info
    const defaultStudentInfo = {
        fname: "",
        initial: "",
        lname: "",
        email: "",
        dob: "",
        suffix: "",
        phone: "",
    };

    // Initialize formData state with student data, filling in defaults where necessary
    const [formData, setFormData] = useState(defaultStudentInfo);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prevState) => ({
            ...prevState,
            [name]: value,
        }));
    };

    const profileSchema = yup
        .object({
            fname: yup.string().required("First name is required"),
            initial: yup.string(), // Assuming initials are optional
            lname: yup.string().required("Last name is required"),
            email: yup
                .string()
                .email("Must be a valid email")
                .required("Email is required"),
            dob: yup.date().required("Date of birth is required"), // Make sure your DatePicker can handle Date objects or format it accordingly
            suffix: yup
                .string()
                .required("Suffix is required")
                .notOneOf(["Select Suffix"], "Select a valid suffix"),
            phone: yup.string().matches(/^[0-9]{10}$/, {
                message: "Phone number must be 10 digits",
                excludeEmptyString: true,
            }),
        }).required();

    /**
     * @TODO Update this function to send the form data to the server
     * @param data
     */
    const updateProfile = async (data) => {
        
        // format data 
        data.dob = new Date(data.dob).toISOString().split("T")[0];
        data.user_id = student.id;
        
        try {
            const response = await apiClient.post(profileEndpoint, data);
            console.log(response.data);
            if (response.data.success)
                toast.success("Profile updated successfully");
            else
                toast.error(
                    "Failed to update profile: " + response.data.message
                );
        } catch (error) {
            console.error("There was an error updating the profile:", error);
            // Handle error (e.g., showing an error message)
            toast.error("Failed to update profile");
        }
    };

    const formatTitle = (title) => {
        const titles = {
            fname: "First Name",
            initial: "Middle Name",
            lname: "Last Name",
            email: "Email",
            dob: "Date of Birth",
            suffix: "Suffix",
            phone: "Phone",
        };
        return titles[title] || title;
    };

    const methods = useForm({
        resolver: yupResolver(profileSchema),
        defaultValues: formData, // Use formData as the source of truth for default values
    });

    useEffect(() => {
        if (student) {
            const user_id = student.id;
            const dob = student?.student_info?.dob
                ? new Date(student?.student_info?.dob)
                      .toISOString()
                      .split("T")[0]
                : "";
            const initial = student?.student_info?.initial || "";
            const suffix = student?.student_info?.suffix || "";
            const phone = student?.student_info?.phone || "";

            const {
                is_active,
                role_id,
                created_at,
                updated_at,
                avatar,
                use_gravatar,
                student_unit_id,
                validations,
                student_info,
                ...restOfStudent
            } = student;

            const newFormData = {
                ...defaultStudentInfo, // Start with default values
                ...restOfStudent,
                dob,
                initial,
                suffix,
                phone,
            };

            setFormData(newFormData);
            methods.reset(newFormData); // Update form default values
        }
    }, [student, methods]);

    if (!formData) {
        return <Loader />;
    }

    return (
        <div className="profile-edit-form">
            <ToastContainer />
            <FormProvider {...methods}>
                <form onSubmit={methods.handleSubmit(updateProfile)}>
                    {Object.entries(formData).map(([key, value]) => {
                        console.log("Key: ", key, "Value: ", value);
                        if (key === "id") {
                            return (
                                <input
                                    key={key}
                                    type="hidden"
                                    name="user_id"
                                    value={value}
                                />
                            );
                        } else if (key === "suffix") {
                            return (
                                <div key={key} className="form-group">
                                    <Select
                                        key={key}
                                        id={key}
                                        title={formatTitle(key)}
                                        value={value}
                                        options={suffixes.map((suffix) => ({
                                            text: suffix,
                                            value: suffix,
                                        }))}
                                        required={false}
                                    />
                                </div>
                            );
                        } else if (key === "dob") {
                            return (
                                <DatePicker
                                    key={key}
                                    id={key}
                                    value={value}
                                    title={formatTitle(key)}
                                    required={true}
                                />
                            );
                        } else {
                            return (
                                <TextInput
                                    key={key}
                                    id={key}
                                    title={formatTitle(key)}
                                    value={value}
                                    required={true}
                                />
                            );
                        }
                    })}
                    <button type="submit" className="btn btn-primary">
                        Update Profile
                    </button>
                </form>
            </FormProvider>
        </div>
    );
};

export default ProfileEdit;
