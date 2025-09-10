import React, { useEffect, useState } from "react";
import ClassroomDashboard from "./ClassroomDashboard";

const StudentDataLayer: React.FC = () => {
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        console.log("🎓 StudentDataLayer: Component rendering...");
        console.log("🎓 StudentDataLayer: Initializing data layer");

        setMounted(true);

        // Mock student data
        const mockUserData = {
            id: 1,
            name: "John Student",
            email: "john@example.com",
            role: "student",
        };

        const mockCourseData = [
            {
                id: 1,
                title: "Introduction to Programming",
                progress: 65,
                status: "in-progress",
            },
            {
                id: 2,
                title: "Web Development Basics",
                progress: 30,
                status: "in-progress",
            },
        ];

        console.log("🎓 StudentDataLayer: Mock user data:", mockUserData);
        console.log("🎓 StudentDataLayer: Mock course data:", mockCourseData);
        console.log("🎓 StudentDataLayer: Data layer mounted successfully");
    }, []);

    return <ClassroomDashboard />;
};

export default StudentDataLayer;
