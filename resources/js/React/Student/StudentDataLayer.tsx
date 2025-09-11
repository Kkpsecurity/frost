import React, { useEffect, useState } from "react";
import StudentDashboard from "./Components/StudentDashboard";
import { LaravelPropsReader } from "./utils/LaravelPropsReader";
import { StudentDashboardData, ClassDashboardData } from "./types/LaravelProps";

const StudentDataLayer: React.FC = () => {
    const [mounted, setMounted] = useState(false);
    const [studentData, setStudentData] = useState<StudentDashboardData | null>(
        null
    );
    const [classData, setClassData] = useState<ClassDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        console.log("🎓 StudentDataLayer: Component rendering...");
        console.log("🎓 StudentDataLayer: Initializing data layer");

        // Read Laravel props from DOM - for student dashboard we only need student data
        const studentPropsData = LaravelPropsReader.readStudentProps();

        console.log(
            "🎓 StudentDataLayer: Student props data:",
            studentPropsData
        );

        if (studentPropsData) {
            setStudentData(studentPropsData);
        } else {
            // Use fallback defaults
            setStudentData(LaravelPropsReader.getSafeStudentData());
        }

        // Try to read classroom data from Laravel props, fallback to empty if not available
        const classPropsData = LaravelPropsReader.readClassProps();

        if (classPropsData) {
            console.log(
                "🎓 StudentDataLayer: Class props data:",
                classPropsData
            );
            setClassData(classPropsData);
        } else {
            // Set default empty class data (OFFLINE state)
            console.log(
                "🎓 StudentDataLayer: No class data available, setting OFFLINE state"
            );
            setClassData({
                instructor: null,
                course_dates: [],
            });
        }
        setIsLoading(false);
        setMounted(true);

        console.log("🎓 StudentDataLayer: Data layer mounted successfully");
    }, []);

    if (!mounted || isLoading) {
        return (
            <div
                className="d-flex justify-content-center align-items-center"
                style={{ height: "16rem" }}
            >
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">
                        Loading dashboard data...
                    </span>
                </div>
            </div>
        );
    }

    if (!studentData) {
        return (
            <div className="alert alert-warning m-4" role="alert">
                <h4 className="alert-heading">Data Loading Issue</h4>
                <p>
                    Unable to load student dashboard data from Laravel props.
                    Please refresh the page.
                </p>
            </div>
        );
    }

    return (
        <StudentDashboard
            studentData={studentData}
            classData={classData || { instructor: null, course_dates: [] }}
        />
    );
};

export default StudentDataLayer;
