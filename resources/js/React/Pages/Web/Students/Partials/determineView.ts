import React, { useCallback, useContext, useEffect, useState } from "react";
import {
    ClassDataShape,
    LaravelDataShape,
    StudentRequirementsType,
} from "../../../../Config/types";
import { ClassContext } from "../../../../Context/ClassContext";
import extractFileName from "../../../../Helpers/extractFileName";

// Constants
const OFFLINE_DASHBOARD = "offline-classroom";
const STUDENT_EJECTED = "student-ejected";
const AGREEMENT_REQUIRED = "agreement-required";
const CLASS_RULES_AGREEMENT_REQUIRED = "class-rules";
const PENDING_VERIFICARION = "pending-verification";
const WAITING_FOR_CLASS = "waiting-room";
const CLASSROOM_VIEW = "virtual-class";
const DAY_COMPLETED = "day-completed";
const DEFAULT_IMAGE = "no-image.jpg";

export const useDetermineView = ({
    laravel,
    studentRequirements,
    setStudentRequirements,
    debug = true,
}) => {
    const [activeView, setActiveView] = useState<string | null>(
        OFFLINE_DASHBOARD
    );

    const ClassData = useContext(ClassContext);

    /**
     * LogedIn Student
     */
    const student = laravel.user;

    if (ClassData?.studentUnit?.ejected_at) return STUDENT_EJECTED;
   

    console.log("Student Requirements: ", student.validations);

    // Check if photo uploads are completed
    const photoUploadsCompleted = useCallback((): boolean => {
        const idcardFileName = extractFileName(laravel.user.validations.idcard);
        const headshotFileName = extractFileName(
            laravel.user.validations.headshot
        );
            
        return (
            headshotFileName === DEFAULT_IMAGE ||
            idcardFileName === DEFAULT_IMAGE
        );
    }, [extractFileName, laravel.user.validations]);

    // Helper function to check if class rules are agreed for each day
    const agreedToClassRules = (): boolean => {
        const agreedToRules = localStorage.getItem("agreedToRules");
        const todayDate = new Date().toISOString().slice(0, 10);
        return agreedToRules === todayDate;
    };

    // Validate live student requirements
    const validateLiveStudentRequirements = useCallback((): string => {
        // Check for student ejection
        if (ClassData?.studentUnit?.ejected_at) return STUDENT_EJECTED;

        /**
         * Note: althought this is null the student_info could have data
         */
        console.log("CLassDataCourseAuth", ClassData?.courseAuth);
        if (ClassData?.courseAuth?.agreed_at === null) return AGREEMENT_REQUIRED;

        // Check for class rules agreement
        if (agreedToClassRules() === false)
            return CLASS_RULES_AGREEMENT_REQUIRED;

        // Check if photo uploads are completed
        if (photoUploadsCompleted() === true) return PENDING_VERIFICARION;

        // Update student requirements only if necessary
        setStudentRequirements((prev: ClassDataShape) => {
            const updates: any = {};

            // Check if student agreement needs to be updated
            if (!prev.studentAgreement?.agreed) {
                updates.studentAgreement = { agreed: true };
            }

            // Check if class rules agreement needs to be updated
            if (!prev.classRulesAgreement?.agreedToRules) {
                updates.classRulesAgreement = {
                    agreedToRules: localStorage.getItem("agreedToRules"),
                };
            }

            // Check if identity verification needs to be updated
            if (
                prev.identityVerification.headshot !==
                    laravel.user.validations.headshot ||
                prev.identityVerification.idcard !==
                    laravel.user.validations.idcard
            ) {
                updates.identityVerification = {
                    ...prev.identityVerification,
                    headshot: laravel.user.validations.headshot,
                    idcard: laravel.user.validations.idcard,
                };
            }

            return Object.keys(updates).length > 0
                ? { ...prev, ...updates }
                : prev;
        });

        /**
         * Check if the student unit and studnet lesson are set
         * To Determens if user in class
         */
        if (!ClassData?.studentLesson && ClassData?.lessonInProgress)
            return WAITING_FOR_CLASS;

        // Default view if all requirements are met
        return CLASSROOM_VIEW;
    }, [ClassData, agreedToClassRules, photoUploadsCompleted, laravel, setStudentRequirements]);

   

    // Determine active view
    useEffect(() => {
        let view: string | null = null;
        if (ClassData?.is_live_class) {
            view = validateLiveStudentRequirements();
        } else if (
            ClassData?.is_live_class &&
            ClassData?.instUnit?.completed_at !== null
        ) {
            view = DAY_COMPLETED;
        } else {
            view = OFFLINE_DASHBOARD;
        }
        setActiveView(view);
    }, [ClassData, agreedToClassRules]); // Dependency array ensures this runs only when ClassData changes

    console.log("Active View: ", activeView);
    return activeView;
};
