import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { ClassDataShape } from "../../Config/types";
import apiConfig from "../../Config/axios";

/**
 * Gets The Data
 * @returns
 */
interface ClassRoomDataProps {
    course_auth_id: number;
    course_date_id: number;
}

/**
 * Maintainint the data set the data is more simple
 * it does not need to be updated as often
 *
 * cacheTime: 1800000, // cache for 30 minutes
 * staleTime: 1800000, // allow stale data for 30 minutes
 * refetchOnMount: true, We do this so that the data is always fresh
 * refetchOnWindowFocus: true, // refetch on window focus
 *
 * @param param0
 * @returns
 */


export const useClassRoomData = (course_auth_id: string, isOnline: boolean) => {
    return useQuery<ClassDataShape>(
        ["classroomData", course_auth_id],
        async () => {
            const response = await apiConfig.get(`/classroom/portal/classdata/${course_auth_id}`);
            console.log("Classroom DataFetch: ", response.data);
            return response.data;
        },
        {            
            cacheTime: 20000, // cache for 20 seconds
            staleTime: 14000, // allow stale data for 14 seconds
            refetchOnMount: false, // do not necessarily refetch on mount
            refetchOnWindowFocus: true, // refetch on window focus
            refetchInterval: 5000, // poll every 15 seconds if online, else every 60 seconds
        }        
    );
};

type UploadFileResponse = {
    success: boolean;
    message: string;
};

type UploadFileToServer = (formData: FormData) => Promise<UploadFileResponse>;

const uploadFileToServer: UploadFileToServer = async (formData) => {
    try {
        const response = await apiConfig.post(
            "/classroom/portal/save_id_data",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        return response.data;
    } catch (error) {
        console.error("Axios Error: ", error);
        throw error;
    }
};

/**
 * Mutates The Data for the Uploaded File
 * @returns
 */
export const HandelFileUpload = () => {
    const queryClient = useQueryClient();
    const { isLoading, isError, data, mutate } = useMutation(
        uploadFileToServer,
        {
            onError: (error) => {
                console.log("Mutation Error: ", error);
            },
            onSuccess: () => {
                queryClient.invalidateQueries(["classroomData"]);
            },
        }
    );

    return {
        isLoading,
        isError,
        data,
        mutate,
    };
};
