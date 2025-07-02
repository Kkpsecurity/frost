import React, { useEffect, useState } from "react";
import apiClient from "../../Config/axios";
import { ToastContainer, toast } from "react-toastify"; // Assuming you're using react-toastify for toasts
import "react-toastify/dist/ReactToastify.css";
import * as yup from "yup";
import { StudentType } from "../../Config/types";

import BanModal from "../../Hooks/Admin/BanModal";
import BanStudentButton from "../../Hooks/Admin/BanStudentButton";

interface UseBanStudentHookProps {
    student: StudentType;
}

const useBanStudentHook = ({ student }: UseBanStudentHookProps) => {
    const [isStudentBanned, setIsStudentBanned] = useState<boolean>(false);
    const [banLoading, setBanLoading] = useState<boolean>(false);
    const [showBanModal, setShowBanModal] = useState<boolean>(false);

    const HandleBanStudent = async (postData) => {
        // use yup to validate the student unit id
        const schema = yup.object().shape({
            studentUnitId: yup.number().required(),
            banReason: yup.string().required(),
        });

        try {
            await schema.validate(postData, { abortEarly: false });
        } catch (error) {
            return toast.error(error.errors[0]);
        }

        setBanLoading(true);
        try {
            const response = await apiClient.post(
                `/admin/instructors/student_tools/${postData.studentUnitId}/ban`,
                { banReason: postData.banReason } // Include banReason in the request body
            );

            if (response.data.success) {
                setTimeout(() => {
                    setIsStudentBanned(true);
                    toast.success(response.data.message);
                    setShowBanModal(false);
                }, 3000);
            } else {
                toast.error("Failed to ban the student.");
            }
        } catch (error) {
            // Assuming error.response.data.message exists; you might need to adjust based on your error structure
            toast.error(
                error.response?.data?.message || "An unexpected error occurred."
            );
        } finally {
            setBanLoading(false);
        }
    };
        
    useEffect(() => {
        const isBanned = !!student?.courseAuth?.disabled_at;
        setIsStudentBanned(isBanned);
    }, [student]);

    return {
        banLoading,
        isStudentBanned,
        BanModal,
        showBanModal,
        setShowBanModal,
        BanStudentButton,
        setBanLoading,
        HandleBanStudent,
    };
};

export default useBanStudentHook;
