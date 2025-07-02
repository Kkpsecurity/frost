import React, { useEffect } from "react";
import * as yup from "yup";
import { StudentLessonType, StudentType } from "../../Config/types";
import apiClient from "../../Config/axios";

import DNCModal from "./DNCModal";
import RevokeDNCButton from "./RevokeDNCButton";

import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { is } from "@babel/types";

interface UseRevokeDNCHookProps {
    student: StudentType;
    activeLesson: number | null;
    studentLesson: StudentLessonType;
}

const useRevokeDNCHook = ({
    student,
    activeLesson,
    studentLesson,
}: UseRevokeDNCHookProps) => {
    const [dncLoading, setDncLoading] = React.useState<boolean>(false);
    const [showDNCModal, setShowDNCModal] = React.useState<boolean>(false);
    const [isDNCed, setIsDNCed] = React.useState<boolean>(false);

    const HandleRevokeDNC = async () => {
        setDncLoading(true);

        // validate with yup
        const schema = yup.object().shape({
            lessonId: yup.number().required(),
            studentUnitId: yup.number().required(),
        });

        try {
            await schema.validate(
                {
                    lessonId: activeLesson,
                    studentUnitId: student.student_unit_id,
                },
                { abortEarly: false }
            );

            const response = await apiClient.post(
                "admin/instructors/student_tools/revoke-dnc",
                {
                    lessonId: activeLesson,
                    studentUnitId: student.student_unit_id,
                }
            );

            if (response.data) {
                toast.success("Student has been DNC'd");
            }

            setTimeout(() => {
                setDncLoading(false);
                setShowDNCModal(false);
            }, 3000);
        } catch (error) {
            toast.error(error);
            return;
        }
    };

    const ConfirmRevokeDNC = async () => {
        setShowDNCModal(true);
    };

    useEffect(() => {
        if (activeLesson && studentLesson && studentLesson.dnc_at) {
            setIsDNCed(true);
        }
    }, [studentLesson]);

    return {
        isDNCed,
        DNCModal,
        dncLoading,
        HandleRevokeDNC,
        ConfirmRevokeDNC,
        RevokeDNCButton,
        showDNCModal,
        setShowDNCModal,
    };
};

export default useRevokeDNCHook;
