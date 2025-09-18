import React, { useEffect, useState } from "react";
import StudentDashboard from "./Components/StudentDashboard";
import { LaravelPropsReader } from "./utils/LaravelPropsReader";
import { StudentDashboardData, ClassDashboardData } from "./types/LaravelProps";
import PageLoader from "../Components/Widgets/PageLoader";
import { Alert } from "react-bootstrap";

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
        return <PageLoader />;
    }

    // No student data from Laravel - authentication or data issue
    if (!studentData) {
        return (
            <Alert variant="warning" className="m-4">
                <Alert.Heading>Data Loading Issue</Alert.Heading>
                <p>
                    Unable to load student dashboard data. Please refresh the
                    page or contact support.
                </p>
            </Alert>
        );
    }

    // No student object in the data - authentication issue
    if (!studentData.student) {
        return (
            <Alert variant="danger" className="m-4">
                <Alert.Heading>Authentication Required</Alert.Heading>
                <p>Please log in to access your student dashboard.</p>
            </Alert>
        );
    }

    // Debug the course_auths data before passing to component
    console.log(
        "🎓 StudentDataLayer: studentData.course_auths:",
        studentData.course_auths
    );
    // Student exists - render dashboard (will handle empty course_auths internally)
    return (
        <StudentDashboard
            student={studentData.student}
            courseAuths={studentData.course_auths || []}
            lessons={studentData.lessons}
            hasLessons={studentData.has_lessons || false}
        />
    );
};

export default StudentDataLayer;
