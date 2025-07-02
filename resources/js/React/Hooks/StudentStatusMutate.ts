import { useMutation, useQueryClient } from "@tanstack/react-query";
import apiClient from "../Config/axios";
import { toast } from "react-toastify";

const doEject = async (formData) => {
    try {
        const { studentUnitId, ejectReason } = formData;

        // Validation
        if (ejectReason === "") {
            throw new Error("Eject reason is required");
        }

        if (studentUnitId === undefined) {
            throw new Error("Student unit id is required");
        }

        // Perform ejection
        const response = await apiClient.post("/services/student_tools/eject", {
            studentUnitId: studentUnitId,
            ejectReason: ejectReason,
        });

        // Check response
        if (response.data.success) {
            // Ejection successful
            toast.success(response.data.message, { autoClose: 5000 });
          
        } else {
            // Ejection failed
            throw new Error(response.data.message);
        }
    } catch (error) {
        // Handle errors
        console.error(error.message);
        toast.error(error.message);
        throw error;
    }
};

export const useHandleEject = () => {
    const queryClient = useQueryClient();

    const { mutate, isLoading } = useMutation(doEject, {
        onError: (error) => {
            console.log("Mutation Error: ", error);
        },
        onSuccess: () => {
            // Invalidate the 'student' query after successful ejection
            queryClient.invalidateQueries(["students"]);
        },
    });

    const handleEject = (formData) => {
        // Call the mutation function
        mutate(formData);
    };

    return {
        handleEject,
        isLoading,
    };
};
