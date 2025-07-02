import React, { createContext } from "react";
import { StudentType, UserListBlockType } from "../../Config/types";

export interface InstructorVideoInterface {
    makeCall: boolean;
    allStudents: StudentType[] | null;
    allQueueStudents: UserListBlockType[] | null;
    activeCallRequest: boolean;
    callStudentId: number | null;
    acceptUserId: number | null;
    callHasEnded: boolean;
    courseDateId: number;
    laravelUserId: number;
    isInstructor: boolean;

    handleCallStudent: (
        studentId: number,
        studentAuthId: number,
        courseDateId: number
    ) => void;
    handleEndCall: (studentId: number) => void;
}

export interface StudentVideoInterface {
    makeCall: boolean;
    isInstructor: boolean;
    callAccepted: boolean;

    handleMakeCall: () => void;
    handleEndCall: () => void;
    handleAcceptCall: () => void;
}

/**
 * Context for the Instructor Video Call
 */
export const InstructorVideoContext = React.createContext<InstructorVideoInterface | {}>({});

/**
 * Context for the Student Video Call
 */
export const StudentVideoContext = React.createContext<StudentVideoInterface | {}>({});

/**
 * Provider for the Instructor Video Call
 */
export const FrostInstructorVideoProvider = InstructorVideoContext.Provider;

/**
 * Provider for the Student Video Call
 */
export const FrostStudentVideoProvider = StudentVideoContext.Provider;
