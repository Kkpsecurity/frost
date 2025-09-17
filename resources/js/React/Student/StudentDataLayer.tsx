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

        // Read Laravel props from DOM - no fallbacks, real data only
        const studentPropsData = LaravelPropsReader.readStudentProps();

        console.log(
            "🎓 StudentDataLayer: Student props data:",
            studentPropsData
        );

        // Set student data (can be null if no real data)
        setStudentData(studentPropsData);

        // Try to read classroom data from Laravel props
        const classPropsData = LaravelPropsReader.readClassProps();

        console.log("🎓 StudentDataLayer: Class props data:", classPropsData);

        // Set class data (can be null if no real data)
        setClassData(classPropsData);
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

    // No student data from Laravel - authentication or data issue
    if (!studentData) {
        return (
            <div className="alert alert-warning m-4" role="alert">
                <h4 className="alert-heading">Data Loading Issue</h4>
                <p>
                    Unable to load student dashboard data. Please refresh the
                    page or contact support.
                </p>
            </div>
        );
    }

    // No student object in the data - authentication issue
    if (!studentData.student) {
        return (
            <div className="alert alert-danger m-4" role="alert">
                <h4 className="alert-heading">Authentication Required</h4>
                <p>Please log in to access your student dashboard.</p>
            </div>
        );
    }

    // Debug the course_auths data before passing to component
    console.log("🎓 StudentDataLayer: About to render StudentDashboard");
    console.log(
        "🎓 StudentDataLayer: studentData.course_auths:",
        studentData.course_auths
    );
    console.log(
        "🎓 StudentDataLayer: course_auths type:",
        typeof studentData.course_auths
    );
    console.log(
        "🎓 StudentDataLayer: course_auths length:",
        studentData.course_auths?.length
    );

    // Student exists - render dashboard (will handle empty course_auths internally)
    return (
        <StudentDashboard
            student={studentData.student}
            courseAuths={studentData.course_auths || []}
        />
    );
};

export default StudentDataLayer;
