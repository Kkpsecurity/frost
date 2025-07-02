import React, { useEffect, useState } from "react";
import apiClient from "../../Config/axios";
import { toast } from "react-toastify";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import {
    CourseAuthType,
    InstUnitType,
    StudentLessonType,
    StudentType,
    StudentUnitType,
} from "../../Config/types";

interface StudentClassroomProps {
    student: StudentType;
    courseAuth: CourseAuthType | null;
    studentLesson: StudentLessonType | null;
    courseDateId: number | null;
    studentUnit: StudentUnitType | null;
    instUnit: InstUnitType | null;
    activeLessonId: number | null;
}

/**
 * @param param0
 * @returns
 */
const useStudentClassroomStatusHook = ({
    student,
    courseAuth,
    studentLesson,
    studentUnit,
    instUnit,
    activeLessonId,
}: StudentClassroomProps) => {

    console.log("StudentClassroomProps", studentLesson);
    
    const [isStudentBanned, setIsStudentBanned] = useState<boolean>(false);
    const [isStudentEjected, setIsStudentEjected] = useState<boolean>(false);
    const [isStudentPresent, setIsStudentPresent] = useState<boolean>(false);
    const [isStudentVerified, setIsStudentVerified] = useState<boolean>(false);

    const [studentCurrentStatus, setStudentCurrentStatus] =
        useState<string>("");

    const [isClassInSession, setIsClassInSession] = useState<boolean>(false);
    const [studentProgress, setStudentProgress] = useState("");

    useEffect(() => {
        setIsClassInSession(instUnit ? true : false);
    }, [instUnit]);

    /**
     * Checks if student has been banned
     * Notes: there is no active class required for this function to work
     * @param courseAuth
     * @returns
     */
    const StudentBanned = (courseAuth: CourseAuthType | null): boolean => {
        const isBanned = courseAuth?.disabled_at ? true : false;
        setIsStudentBanned(isBanned);
        return isBanned;
    };

    /**
     * Check if student is present for the day
     * @param student
     * @returns
     */
    const StudentPresent = (studentUnit: StudentUnitType | null): boolean => {
        const isPresent = studentUnit ? true : false;
        setIsStudentPresent(isPresent);
        return isPresent;
    };

    /**
     * Checks if the Student has been Ejected for the Day
     * @param student
     * @returns
     */
    const StudentEjected = (studentUnit: StudentUnitType | null): boolean => {
        const isEjected = !!studentUnit?.ejected_at;
        setIsStudentEjected(isEjected);
        return isEjected;
    };

    /**
     *
     * @param student
     * @returns
     */
    const StudentPhotosUploaded = (student: StudentType): boolean => {
        if (!student || !student.validations) return false;

        const today = new Date().setHours(0, 0, 0, 0); // Today's date at midnight for comparison
        const defaultNoImageName = "no-image";
        let isHeadshotMissing = true; 
        let isIdCardMissing = true;

        // Ensure idcard is a string and then check if it includes 'no-image'
        if (typeof student.validations.idcard === "string") {
            isIdCardMissing =
                student.validations.idcard.includes(defaultNoImageName);
        }

        // Check headshots if it's an array and look for today's image
        if (Array.isArray(student.validations.headshot)) {
            // This loop checks for the entry with today's timestamp
            for (const key in student.validations.headshot) {
                const timestamp = new Date(parseInt(key)).setHours(0, 0, 0, 0);
                if (timestamp === today) {
                    const headshotForToday = student.validations.headshot[key];
                    
                    if (
                        typeof headshotForToday === "string" &&
                        !headshotForToday.includes(defaultNoImageName)
                    ) {
                        isHeadshotMissing = false;
                        break;
                    }
                }
            }
        }

        // Return true if both images are present and not default "no-image"
        return !isHeadshotMissing || !isIdCardMissing;
    };

    /**
     * Checks to see if the Student is Present for the Lesson
     */
    const StudentPresentForLesson = (
        studentLesson: StudentLessonType | null,
        activeLesson: number | null
    ): boolean => {
        if (!activeLesson || activeLesson === 0) return null;
        else if (studentLesson?.lesson_id === activeLesson) {
            return true;
        }

        return false;
    };

    /**
     * the verified fields is a json array of the validation data
     * @returns
     */
    const StudentSignedAgreement = (courseAuth: CourseAuthType | null) => {
        if (courseAuth?.agreed_at !== null) {
            return true;
        }

        return false;
    };

    /**
     * Checks to see if the Student has been DNCed
     * @param studentLesson
     * @returns
     */
    const StudentDNCed = (studentLesson: StudentLessonType | null): boolean => {
        if (studentLesson?.dnc_at) {
            return true;
        }

        return false;
    };

    /**
     * Check to see if the Photos have Been Validated
     * @param student
     * @returns
     */
    const StudentIDValidated = (
        studentUnit: StudentUnitType | null
    ): boolean => {
        const isVerified = !!studentUnit?.verified;
        setIsStudentVerified(isVerified);
        return isVerified;
    };
    
    useEffect(() => {
        
        if (!isClassInSession) {
            setStudentCurrentStatus("ClassNotInSession");
            return;
        }

        // Check for various status conditions
        if (StudentBanned(courseAuth)) {
            setStudentCurrentStatus("Banned");
        } else if (StudentEjected(studentUnit)) {
            setStudentCurrentStatus("Ejected");
        } else if (StudentDNCed(studentLesson)) {
            setStudentCurrentStatus("DNCed");
        } else if (!StudentPresent(studentUnit)) {
            setStudentCurrentStatus("NotInClass");
        } else {
            setStudentCurrentStatus("Active");
        }

        // Check for various progress conditions
        if (!StudentSignedAgreement(courseAuth)) {
            setStudentProgress("AgreementNotSigned");
        } else if (!StudentPhotosUploaded(student)) {
            setStudentProgress("PhotosMissing");
        } else if (!StudentIDValidated(studentUnit)) {
            setStudentProgress("IDNotValidated");
        } else if (!StudentPresentForLesson(studentLesson, activeLessonId)) {
            setStudentProgress("LessonNotPresent");
        } else {
            setStudentProgress("ProgressComplete");
        }
    }, [
        student,
        studentLesson,
        courseAuth,
        studentUnit,
        isClassInSession,
        activeLessonId,
    ]);

    return {
        studentCurrentStatus,
        studentProgress,
        isStudentPresent,
    };
};

export default useStudentClassroomStatusHook;
