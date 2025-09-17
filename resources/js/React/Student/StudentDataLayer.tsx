import React, { useEffect, useState } from "react";
import StudentDashboard from "./Components/StudentDashboard";
import StudentClassroom from "./Components/StudentClassroom";
import { LaravelPropsReader } from "./utils/LaravelPropsReader";
import {
    StudentDashboardData,
    ClassDashboardData,
    CourseAuth,
} from "./types/LaravelProps";

// SPA View types
type StudentView = "DASHBOARD" | "CLASSROOM";

// Navigation state persistence keys
const NAVIGATION_STATE_KEY = "frost_student_navigation_state";

interface NavigationState {
    view: StudentView;
    courseAuthId?: number;
    activeTab?: string;
}

const StudentDataLayer: React.FC = () => {
    const [mounted, setMounted] = useState(false);
    const [currentView, setCurrentView] = useState<StudentView>("DASHBOARD");
    const [selectedCourseAuth, setSelectedCourseAuth] =
        useState<CourseAuth | null>(null);
    const [studentData, setStudentData] = useState<StudentDashboardData | null>(
        null
    );
    const [classData, setClassData] = useState<ClassDashboardData | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    // Save navigation state to localStorage
    const saveNavigationState = (state: NavigationState) => {
        try {
            localStorage.setItem(NAVIGATION_STATE_KEY, JSON.stringify(state));
        } catch (error) {
            console.warn("Failed to save navigation state:", error);
        }
    };

    // Load navigation state from localStorage
    const loadNavigationState = (): NavigationState | null => {
        try {
            const saved = localStorage.getItem(NAVIGATION_STATE_KEY);
            return saved ? JSON.parse(saved) : null;
        } catch (error) {
            console.warn("Failed to load navigation state:", error);
            return null;
        }
    };

    // Clear navigation state from localStorage
    const clearNavigationState = () => {
        try {
            localStorage.removeItem(NAVIGATION_STATE_KEY);
        } catch (error) {
            console.warn("Failed to clear navigation state:", error);
        }
    };

    // Navigation functions for SPA behavior
    const navigateToClassroom = (courseAuth: CourseAuth) => {
        console.log(
            "🏫 StudentDataLayer: Navigating to classroom for course:",
            courseAuth
        );
        setSelectedCourseAuth(courseAuth);
        setCurrentView("CLASSROOM");

        // Save navigation state for persistence on refresh
        const navigationState: NavigationState = {
            view: "CLASSROOM",
            courseAuthId: courseAuth.id,
            activeTab: "dashboard", // Default tab
        };
        saveNavigationState(navigationState);

        // Update URL without page reload
        window.history.pushState(
            { view: "CLASSROOM", courseAuthId: courseAuth.id },
            "",
            `/classroom/enter/${courseAuth.id}`
        );
    };

    const navigateToDashboard = () => {
        console.log("📋 StudentDataLayer: Navigating back to dashboard");
        setSelectedCourseAuth(null);
        setCurrentView("DASHBOARD");

        // Clear navigation state when returning to dashboard
        clearNavigationState();

        // Update URL without page reload
        window.history.pushState({ view: "DASHBOARD" }, "", "/classroom");
    };

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

        // Check for persisted navigation state after data is loaded
        if (studentPropsData?.course_auths) {
            const savedState = loadNavigationState();
            console.log(
                "🔄 StudentDataLayer: Checking saved navigation state:",
                savedState
            );

            if (
                savedState &&
                savedState.view === "CLASSROOM" &&
                savedState.courseAuthId
            ) {
                // Find the course auth from saved state
                const courseAuth = studentPropsData.course_auths.find(
                    (auth) => auth.id === savedState.courseAuthId
                );

                if (courseAuth) {
                    console.log(
                        "🔄 StudentDataLayer: Restoring classroom view for course:",
                        courseAuth
                    );
                    setSelectedCourseAuth(courseAuth);
                    setCurrentView("CLASSROOM");

                    // Update URL to match restored state
                    window.history.replaceState(
                        { view: "CLASSROOM", courseAuthId: courseAuth.id },
                        "",
                        `/classroom/enter/${courseAuth.id}`
                    );
                } else {
                    console.warn(
                        "🔄 StudentDataLayer: Course auth not found for saved state, clearing"
                    );
                    clearNavigationState();
                }
            }
        }

        setIsLoading(false);
        setMounted(true);

        console.log("🎓 StudentDataLayer: Data layer mounted successfully");
    }, []);

    // Handle browser back/forward buttons
    useEffect(() => {
        const handlePopState = (event: PopStateEvent) => {
            if (event.state) {
                if (event.state.view === "CLASSROOM") {
                    const courseAuth = studentData?.course_auths?.find(
                        (auth) => auth.id === event.state.courseAuthId
                    );
                    if (courseAuth) {
                        setSelectedCourseAuth(courseAuth);
                        setCurrentView("CLASSROOM");
                    }
                } else {
                    setCurrentView("DASHBOARD");
                    setSelectedCourseAuth(null);
                }
            }
        };

        window.addEventListener("popstate", handlePopState);
        return () => window.removeEventListener("popstate", handlePopState);
    }, [studentData]);

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
    console.log(`🎓 StudentDataLayer: About to render ${currentView}`);
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
    console.log("🎓 StudentDataLayer: currentView:", currentView);
    console.log("🎓 StudentDataLayer: selectedCourseAuth:", selectedCourseAuth);

    // Render based on current view
    if (currentView === "CLASSROOM" && selectedCourseAuth) {
        // Find the course data for the selected CourseAuth
        const courseData = selectedCourseAuth.course || {
            id: selectedCourseAuth.course_id,
            title: "Course",
            description: "",
            slug: "course",
        };

        return (
            <StudentClassroom
                student={studentData.student}
                courseAuth={selectedCourseAuth}
                course={courseData}
                onBackToDashboard={navigateToDashboard}
            />
        );
    }

    // Default: render dashboard with navigation handlers
    return (
        <StudentDashboard
            student={studentData.student}
            courseAuths={studentData.course_auths || []}
            onEnterClassroom={navigateToClassroom}
        />
    );
};

export default StudentDataLayer;
