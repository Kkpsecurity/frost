import React, { useEffect, useState } from "react";
import StudentSearch from "./Components/StudentSearch";
import StudentDashboard from "./Components/StudentDashboard";

const SupportDataLayer: React.FC = () => {
    const [mounted, setMounted] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [isAdmin, setIsAdmin] = useState(false);
    const [isSysAdmin, setIsSysAdmin] = useState(false);
    const [selectedStudent, setSelectedStudent] = useState<any>(null);

    useEffect(() => {
        console.log("🔧 SupportDataLayer: Component rendering...");
        console.log("🔧 SupportDataLayer: Initializing data layer");

        // Read user role from Laravel props
        const propsElement = document.getElementById("support-props");
        if (propsElement) {
            try {
                const props = JSON.parse(propsElement.textContent || "{}");
                setIsAdmin(props.isAdmin || false);
                setIsSysAdmin(props.isSysAdmin || false);
                console.log("🔧 User roles:", {
                    isAdmin: props.isAdmin,
                    isSysAdmin: props.isSysAdmin,
                });
            } catch (error) {
                console.error("Failed to parse support props:", error);
            }
        }

        setIsLoading(false);
        setMounted(true);

        console.log("🔧 SupportDataLayer: Data layer mounted successfully");
    }, []);

    if (!mounted || isLoading) {
        return <div>Loading...</div>;
    }

    const handleStudentSelect = (user: any) => {
        console.log("Selected user:", user);
        setSelectedStudent(user);
    };

    const handleBackToSearch = () => {
        setSelectedStudent(null);
    };

    return (
        <div className="container-fluid mt-4">
            {!selectedStudent ? (
                <StudentSearch
                    onStudentSelect={handleStudentSelect}
                    isAdmin={isAdmin}
                    isSysAdmin={isSysAdmin}
                />
            ) : (
                <StudentDashboard
                    student={selectedStudent}
                    onBack={handleBackToSearch}
                />
            )}
        </div>
    );
};

export default SupportDataLayer;
