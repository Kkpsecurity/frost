import React, { useEffect, useState } from "react";
import { toast } from "react-toastify";
import apiClient from "../../Config/axios";
import { StudentLessonType, StudentType, StudentUnitType } from "../../Config/types";

import "react-toastify/dist/ReactToastify.css";

import AllowAccessButton from "./AllowAccessButton";
import AllowAccessModal from "./AllowAccessModal";

interface UseAllowAccessHookProps {
    student: StudentType;
    studentUnit: StudentUnitType | null;
    activeLesson: number | null;
}

const useAllowAccessHook = ({
    student,
    studentUnit,
    activeLesson,
}: UseAllowAccessHookProps) => {
    const [allowAccessLoading, setAllowAccessLoading] = useState<boolean>(false);
    const [allowAccessReason, setAllowAccessReason] = useState<string>("");
    const [showAllowAccessModal, setShowAllowAccessModal] = useState<boolean>(false);
    const [isStudentLate, setIsStudentLate] = useState<boolean>(false);
    const [lessonID, setLessonID] = useState<number | null>(null);

    const HandleAllowAccess = async () => {
        setAllowAccessLoading(true);
        if (studentUnit?.id === null || lessonID === null) {
            toast.error("Student or Lesson is not available");
            setAllowAccessLoading(false);
            return;
        } else {
            try {
                const response = await apiClient.post(
                    "/services/student_tools/allow_access",
                    {
                        studentUnitId: studentUnit?.id,
                        lessonId: activeLesson,
                    }
                );

                if (response.data.status === "success") {
                    toast.success(response.data.message);
                    setTimeout(() => {
                        setAllowAccessLoading(false);
                        setShowAllowAccessModal(false);
                    }, 5000);
                } else {
                    setAllowAccessLoading(false);
                    toast.error(response.data.message);
                }
            } catch (error) {
                console.error(error.message);
                toast.error(error.message);
                setAllowAccessLoading(false);
            }
        }
    };

    const ConfirmAllowAccess = (lesson_id: number) => {       
        setShowAllowAccessModal(true);
        setLessonID(lesson_id);
    };

    useEffect(() => {
        if (studentUnit !== null && activeLesson === null) {
            setIsStudentLate(true);
        }
    }, [studentUnit, activeLesson]);

    return {
        isStudentLate,
        ConfirmAllowAccess,
        HandleAllowAccess,
        allowAccessLoading,
        AllowAccessButton,
        AllowAccessModal,
        showAllowAccessModal,
        setShowAllowAccessModal,
        setAllowAccessReason,
    };
};

export default useAllowAccessHook;
