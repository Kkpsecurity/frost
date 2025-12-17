import React, { useEffect, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Alert } from "react-bootstrap";
import MainDashboard from "./Dashboard/MainDashboard";
import PageLoader from "../../Shared/Components/Widgets/PageLoader";
import { StudentContextProvider, StudentContextType } from "../context/StudentContext";
import { ClassroomContextProvider, ClassroomContextType } from "../context/ClassroomContext";
import { fetchClassroomPollData, isInstructorTeaching, getClassroomStatus } from "../services/classroomService";
import { ClassroomPollDataType } from "../types/classroom";

interface StudentDataLayerProps {
    courseAuthId?: number | null;
}

/**
 * StudentDataLayer - Handles all API polling for student portal
 *
 * Responsibilities:
 * - Poll /classroom/student/poll endpoint every 5 seconds
 * - Poll /classroom/classroom/poll endpoint for classroom data
 * - Combine both datasets into contexts
 * - Pass data down via Context providers
 * - Handle loading and error states
 *
 * Does NOT handle:
 * - Business logic (that's in StudentDashboard)
 * - UI rendering beyond loaders/errors
 * - Conditional rendering logic
 * - User interactions
 */
const StudentDataLayer: React.FC<StudentDataLayerProps> = ({
    courseAuthId,
}) => {
    // Fetch student polling data
    const {
        data: studentData,
        isLoading: studentLoading,
        error: studentError,
    } = useQuery({
        queryKey: ["student-poll", courseAuthId],
        queryFn: async () => {
            const response = await fetch(`/classroom/student/poll?course_auth_id=${courseAuthId}`);
            if (!response.ok) {
                throw new Error(`Failed to fetch student data: ${response.status}`);
            }
            return response.json();
        },
        enabled: !!courseAuthId,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });

    // Fetch classroom polling data
    const {
        data: classroomData,
        isLoading: classroomLoading,
        error: classroomError,
    } = useQuery({
        queryKey: ["classroom-poll", courseAuthId],
        queryFn: async () => {
            if (!courseAuthId) throw new Error("courseAuthId is required");
            return fetchClassroomPollData(courseAuthId);
        },
        enabled: !!courseAuthId,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });

    const isLoading = studentLoading || classroomLoading;
    const error = studentError || classroomError;

    // Show loading spinner
    if (isLoading) {
        return <PageLoader />;
    }

    // Show error if polling failed
    if (error) {
        return (
            <Alert variant="danger" className="m-4">
                <Alert.Heading>⚠️ Data Loading Error</Alert.Heading>
                <p>
                    {error instanceof Error
                        ? error.message
                        : "Unable to load student data"}
                </p>
                <p className="mb-0">Please refresh the page or contact support.</p>
            </Alert>
        );
    }

    // No courseAuthId provided
    if (!courseAuthId) {
        return (
            <Alert variant="warning" className="m-4">
                <Alert.Heading>Missing Course Information</Alert.Heading>
                <p>Unable to identify your course enrollment.</p>
                <p className="mb-0">Please return to your dashboard and try again.</p>
            </Alert>
        );
    }

    // Create student context data
    const studentContextValue: StudentContextType = {
        student: studentData?.data?.student || null,
        courses: studentData?.data?.courses || [],
        progress: studentData?.data?.progress || null,
        notifications: studentData?.data?.notifications || [],
        assignments: studentData?.data?.assignments || [],
        loading: false,
        error: null,
    };

    // Create classroom context data from poll response
    const classroomContextValue: ClassroomContextType = {
        data: classroomData || null,
        course: classroomData?.course || null,
        courseDate: classroomData?.courseDate || null,
        instructor: classroomData?.courseDate?.instructor || null,
        instUnit: classroomData?.instUnit || null,
        courseUnits: classroomData?.courseUnit?.course_units || [],
        courseLessons: classroomData?.lessons || [],
        instLessons: classroomData?.instUnit?.inst_lessons || [],
        config: classroomData?.config || null,
        isClassroomActive: classroomData ? isInstructorTeaching(classroomData) : false,
        isInstructorOnline: classroomData?.courseDate?.instructor?.online_status === 'online' || false,
        classroomStatus: classroomData ? (getClassroomStatus(classroomData) as any) : 'not_started',
        loading: false,
        error: null,
    };

    console.log("🎓 StudentDataLayer: Polling data received", {
        studentData: studentContextValue,
        classroomData: classroomContextValue,
    });

    // Render with both contexts providing data down the tree
    return (
        <StudentContextProvider value={studentContextValue}>
            <ClassroomContextProvider value={classroomContextValue}>
                <MainDashboard courseAuthId={courseAuthId} />
            </ClassroomContextProvider>
        </StudentContextProvider>
    );
};

export default StudentDataLayer;
