import React from "react";
import { CourseType } from "../../types/students.types";
import { useStudent } from "../../context/StudentContext";

interface Course {
    id: number;
    course_date_id: number;
    course_name: string;
    start_date: string;
    status: string;
    completion_status?: string;
}

const StudentCoursesTable = ({
    courses,
    formatDate,
}: {
    courses: Course[];
    formatDate: (dateString: string) => string;
}) => {
    const { setSelectedCourseAuthId } = useStudent();

    const handleEnterClassroom = (courseAuthId: number) => {
        // SPA navigation - set selected course in React state
        setSelectedCourseAuthId(courseAuthId);
    };

    return (
        <table
            className="align-middle"
            style={{
                width: '100%',
                marginBottom: 0,
                backgroundColor: "#34495e !important" as any,
                color: "white !important" as any,
                borderCollapse: 'collapse',
            }}
        >
            <thead
                style={{
                    backgroundColor: "#212a3e !important" as any,
                    color: "white !important" as any,
                }}
            >
                <tr>
                    <th
                        style={{
                            padding: "1rem",
                            fontWeight: "600",
                            fontSize: "0.875rem",
                            backgroundColor: "#212a3e",
                            color: "white",
                            border: "none",
                        }}
                    >
                        Course Date
                    </th>
                    <th
                        style={{
                            padding: "1rem",
                            fontWeight: "600",
                            fontSize: "0.875rem",
                            backgroundColor: "#212a3e",
                            color: "white",
                            border: "none",
                        }}
                    >
                        Course Name
                    </th>
                    <th
                        style={{
                            padding: "1rem",
                            fontWeight: "600",
                            fontSize: "0.875rem",
                            backgroundColor: "#212a3e",
                            color: "white",
                            border: "none",
                        }}
                    >
                        Status
                    </th>
                    <th
                        style={{
                            padding: "1rem",
                            fontWeight: "600",
                            fontSize: "0.875rem",
                            textAlign: "center",
                            backgroundColor: "#212a3e",
                            color: "white",
                            border: "none",
                        }}
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                {courses.map((course: Course) => (
                    <tr
                        key={course.id}
                        style={{
                            backgroundColor: "#3d5a6b !important" as any,
                            borderBottom: "1px solid rgba(255,255,255,0.1)",
                        }}
                    >
                        <td
                            style={{
                                padding: "1rem",
                                backgroundColor: "#3d5a6b",
                                color: "white",
                                border: "none",
                            }}
                        >
                            <i
                                className="far fa-calendar-alt me-2"
                                style={{
                                    color: "#95a5a6",
                                }}
                            ></i>
                            {formatDate(course.start_date)}
                        </td>
                        <td
                            style={{
                                padding: "1rem",
                                fontWeight: "500",
                                backgroundColor: "#3d5a6b",
                                color: "white",
                                border: "none",
                            }}
                        >
                            {course.course_name || "N/A"}
                        </td>
                        <td
                            style={{
                                padding: "1rem",
                                backgroundColor: "#3d5a6b",
                                color: "white",
                                border: "none",
                            }}
                        >
                            <span
                                className={`badge ${
                                    course.status === "Completed"
                                        ? "bg-success"
                                        : course.status === "In Progress"
                                        ? "bg-info"
                                        : "bg-warning text-dark"
                                }`}
                                style={{
                                    padding: "0.4rem 0.8rem",
                                    fontSize: "0.75rem",
                                    textTransform: "uppercase",
                                }}
                            >
                                {course.status || "Not Started"}
                            </span>
                        </td>
                        <td
                            style={{
                                padding: "1rem",
                                textAlign: "center",
                                backgroundColor: "#3d5a6b",
                                color: "white",
                                border: "none",
                            }}
                        >
                            <button
                                onClick={() => handleEnterClassroom(course.id)}
                                className="btn btn-primary btn-sm"
                                style={{
                                    padding: "0.5rem 1.25rem",
                                    fontSize: "0.75rem",
                                    fontWeight: "600",
                                    textTransform: "uppercase",
                                }}
                            >
                                <i className="fas fa-arrow-right me-2"></i>
                                Enter Classroom
                            </button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
};

export default StudentCoursesTable;
