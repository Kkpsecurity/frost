import React, { useEffect, useState } from "react";
import * as yup from "yup";

import apiClient from "../../Config/axios";
import EjectStudentButton from "./EjectStudentButton";
import EjectModal from "./EjectModal";

import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { StudentUnitType } from "../../Config/types";

interface EjectStudentProps {
    studentUnit: StudentUnitType | null;
}

const useEjectStudentHook = ({ studentUnit }: EjectStudentProps) => {
    const [isStudentEjected, setIsStudentEjected] = useState<boolean>(false);
    const [ejectLoading, setEjectLoading] = useState<boolean>(false);
    const [showEjectModal, setShowEjectModal] = useState<boolean>(false);
    const [ejectReason, setEjectReason] = useState<string>("");
    const [studentUnitId, setStudentUnitId] = useState<number | null>(null);

    const ConfirmEjectStudent = async (studentUnit) => {
        setShowEjectModal(true);
        setStudentUnitId(studentUnit.id);
    };

    const ejectStudentSchema = yup.object().shape({
        studentUnitId: yup.number().required(),
    });

    const HandleEjectStudent = async () => {
        setEjectLoading(true);

        try {
            const studentUnitId =
                studentUnit && studentUnit.id ? studentUnit.id : null;
            
            await ejectStudentSchema.validate(
                { studentUnitId },
                { abortEarly: true }
            );

            // Perform the API call
            const response = await apiClient.post(
                "admin/instructors/student_tools/eject-student",
                { studentUnitId }
            );

            if (response.status === 200) {
                toast.success("Student has been Ejected");
            } else {
                toast.error("Failed to eject the student");
                throw new Error("Failed to eject the student");
            }
        } catch (error) {
            toast.error(error.message || "An error occurred");
        } finally {
            setTimeout(() => {
                setEjectLoading(false);
                setShowEjectModal(false);
            }, 3500);
        }
    };

    useEffect(() => {
        console.log("Current studentUnit:", studentUnit);
        if (!studentUnit) return;
        setStudentUnitId(studentUnit?.id || null);
    }, [studentUnit]);

    useEffect(() => {
        setIsStudentEjected(studentUnit?.ejected_at ? true : false);
    }, [studentUnit]);

    return {
        ejectLoading,
        studentUnitId,
        showEjectModal,
        isStudentEjected,
        EjectModal,
        setEjectReason,
        setShowEjectModal,
        HandleEjectStudent,
        EjectStudentButton,
        ConfirmEjectStudent,
    };
};

export default useEjectStudentHook;
