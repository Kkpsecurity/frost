import React, { useState } from "react";
import axios from "axios";

interface StudentDetailsData {
    id: number;
    email: string;
    fname: string;
    lname: string;
    student_num: string | null;
    is_active: boolean;
    role_id: number;
    email_opt_in: boolean;
    use_gravatar: boolean;
    created_at: string | null;
    updated_at: string | null;
    student_info: Record<string, any>;
}

interface StudentDetailsProps {
    details: StudentDetailsData | null;
    onUpdate: () => void;
}

const StudentDetails: React.FC<StudentDetailsProps> = ({
    details,
    onUpdate,
}) => {
    const [isEditing, setIsEditing] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [formData, setFormData] = useState<StudentDetailsData | null>(
        details
    );
    const [studentInfoFields, setStudentInfoFields] = useState<
        Array<{ key: string; value: string }>
    >([]);

    // Initialize student_info fields when details change
    React.useEffect(() => {
        if (details?.student_info) {
            const fields = Object.entries(details.student_info).map(
                ([key, value]) => ({
                    key,
                    value: String(value),
                })
            );
            setStudentInfoFields(fields);
        }
        setFormData(details);
    }, [details]);

    if (!details) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No student details available.
            </div>
        );
    }

    const handleInputChange = (field: keyof StudentDetailsData, value: any) => {
        if (formData) {
            setFormData({
                ...formData,
                [field]: value,
            });
        }
    };

    const handleStudentInfoChange = (index: number, value: string) => {
        const newFields = [...studentInfoFields];
        newFields[index].value = value;
        setStudentInfoFields(newFields);
    };

    const handleSave = async () => {
        if (!formData) return;

        setIsSaving(true);
        try {
            // Convert student_info fields array back to object
            const studentInfoObject: Record<string, any> = {};
            studentInfoFields.forEach((field) => {
                if (field.key.trim()) {
                    studentInfoObject[field.key] = field.value;
                }
            });

            const response = await axios.post(
                `/admin/api/support/update-student/${details.id}`,
                {
                    fname: formData.fname,
                    lname: formData.lname,
                    email: formData.email,
                    student_num: formData.student_num,
                    email_opt_in: formData.email_opt_in,
                    student_info: studentInfoObject,
                }
            );

            if (response.data.success) {
                alert("Student details updated successfully!");
                setIsEditing(false);
                onUpdate(); // Refresh poll data
            } else {
                alert(response.data.message || "Failed to update details");
            }
        } catch (error: any) {
            console.error("Failed to update student details:", error);
            const message =
                error.response?.data?.message ||
                "Failed to update student details";
            alert(message);
        } finally {
            setIsSaving(false);
        }
    };

    const handleCancel = () => {
        setFormData(details);
        setIsEditing(false);
        // Reset student_info fields
        if (details?.student_info) {
            const fields = Object.entries(details.student_info).map(
                ([key, value]) => ({
                    key,
                    value: String(value),
                })
            );
            setStudentInfoFields(fields);
        }
    };

    return (
        <div>
            {/* Action Buttons */}
            <div className="mb-3 d-flex justify-content-between align-items-center">
                <h5 className="mb-0">
                    <i className="fas fa-user-edit mr-2"></i>
                    Student Information
                </h5>
                {!isEditing ? (
                    <button
                        className="btn btn-primary"
                        onClick={() => setIsEditing(true)}
                    >
                        <i className="fas fa-edit mr-2"></i>
                        Edit Details
                    </button>
                ) : (
                    <div>
                        <button
                            className="btn btn-success mr-2"
                            onClick={handleSave}
                            disabled={isSaving}
                        >
                            <i
                                className={`fas ${isSaving ? "fa-spinner fa-spin" : "fa-save"} mr-2`}
                            ></i>
                            {isSaving ? "Saving..." : "Save Changes"}
                        </button>
                        <button
                            className="btn btn-secondary"
                            onClick={handleCancel}
                            disabled={isSaving}
                        >
                            <i className="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                    </div>
                )}
            </div>

            {/* Basic Information Card */}
            <div className="card mb-3">
                <div className="card-header bg-primary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-user mr-2"></i>
                        Basic Information
                    </h5>
                </div>
                <div className="card-body">
                    <div className="row">
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                First Name:
                            </label>
                            {isEditing ? (
                                <input
                                    type="text"
                                    className="form-control"
                                    value={formData?.fname || ""}
                                    onChange={(e) =>
                                        handleInputChange("fname", e.target.value)
                                    }
                                />
                            ) : (
                                <div className="form-control-plaintext text-white">
                                    {details.fname}
                                </div>
                            )}
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Last Name:
                            </label>
                            {isEditing ? (
                                <input
                                    type="text"
                                    className="form-control"
                                    value={formData?.lname || ""}
                                    onChange={(e) =>
                                        handleInputChange("lname", e.target.value)
                                    }
                                />
                            ) : (
                                <div className="form-control-plaintext text-white">
                                    {details.lname}
                                </div>
                            )}
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">Email:</label>
                            {isEditing ? (
                                <input
                                    type="email"
                                    className="form-control"
                                    value={formData?.email || ""}
                                    onChange={(e) =>
                                        handleInputChange("email", e.target.value)
                                    }
                                />
                            ) : (
                                <div className="form-control-plaintext text-white">
                                    {details.email}
                                </div>
                            )}
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Student Number:
                            </label>
                            {isEditing ? (
                                <input
                                    type="text"
                                    className="form-control"
                                    value={formData?.student_num || ""}
                                    onChange={(e) =>
                                        handleInputChange(
                                            "student_num",
                                            e.target.value
                                        )
                                    }
                                />
                            ) : (
                                <div className="form-control-plaintext text-white">
                                    {details.student_num || "N/A"}
                                </div>
                            )}
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Account Status:
                            </label>
                            <div className="form-control-plaintext">
                                <span
                                    className={`badge ${details.is_active ? "badge-success" : "badge-danger"}`}
                                >
                                    {details.is_active ? "Active" : "Inactive"}
                                </span>
                            </div>
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Email Opt-in:
                            </label>
                            {isEditing ? (
                                <div className="form-check mt-2">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={formData?.email_opt_in || false}
                                        onChange={(e) =>
                                            handleInputChange(
                                                "email_opt_in",
                                                e.target.checked
                                            )
                                        }
                                    />
                                    <label className="form-check-label">
                                        Subscribed to emails
                                    </label>
                                </div>
                            ) : (
                                <div className="form-control-plaintext">
                                    <span
                                        className={`badge ${details.email_opt_in ? "badge-success" : "badge-secondary"}`}
                                    >
                                        {details.email_opt_in
                                            ? "Subscribed"
                                            : "Not Subscribed"}
                                    </span>
                                </div>
                            )}
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Created At:
                            </label>
                            <div className="form-control-plaintext text-white">
                                {details.created_at
                                    ? new Date(
                                          details.created_at
                                      ).toLocaleString()
                                    : "N/A"}
                            </div>
                        </div>
                        <div className="col-md-6 mb-3">
                            <label className="font-weight-bold">
                                Updated At:
                            </label>
                            <div className="form-control-plaintext text-white">
                                {details.updated_at
                                    ? new Date(
                                          details.updated_at
                                      ).toLocaleString()
                                    : "N/A"}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Student Info JSON Card */}
            <div className="card">
                <div className="card-header bg-info text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-database mr-2"></i>
                        Additional Student Information (JSON)
                    </h5>
                </div>
                <div className="card-body">
                    {isEditing ? (
                        <div>
                            {studentInfoFields.length > 0 ? (
                                <div className="table-responsive">
                                    <table className="table table-bordered">
                                        <thead className="thead-light">
                                            <tr>
                                                <th style={{ width: '40%' }}>Field</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {studentInfoFields.map((field, index) => (
                                                <tr key={index}>
                                                    <td className="font-weight-bold align-middle">
                                                        {field.key}
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            placeholder="Value"
                                                            value={field.value}
                                                            onChange={(e) =>
                                                                handleStudentInfoChange(
                                                                    index,
                                                                    e.target.value
                                                                )
                                                            }
                                                        />
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle mr-2"></i>
                                    No additional student information stored.
                                </div>
                            )}
                        </div>
                    ) : (
                        <div>
                            {Object.keys(details.student_info).length > 0 ? (
                                <div className="table-responsive">
                                    <table className="table table-bordered">
                                        <thead className="thead-light">
                                            <tr>
                                                <th>Field</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {Object.entries(
                                                details.student_info
                                            ).map(([key, value]) => (
                                                <tr key={key}>
                                                    <td className="font-weight-bold">
                                                        {key}
                                                    </td>
                                                    <td>{String(value)}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle mr-2"></i>
                                    No additional student information stored.
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default StudentDetails;
