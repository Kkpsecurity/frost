import apiClient from "../Config/axios";
import { useQuery } from "@tanstack/react-query";
import { StudentType } from "../Config/types";

/**
 * @desc: request a list of queued users that requested a video call
 * @param param0
 * @returns
 */
export const sendCallRequest = ({ course_date_id, user_id }) => {
    return apiClient
        .get(
            "/services/frost_video/student/call_request/" +
                course_date_id +
                "/" +
                user_id
        )
        .then((response) => {
             return response.data;
        })
        .catch((error) => {
            console.error("student call request error:", error.message);
            throw new Error(error.message);
        });
};

/**
 * @desc: Cancels a call request
 * @param param0
 * @returns
 */
export const sendCallCancelRequest = ({ course_date_id, user_id }) => {
    return apiClient
        .get(
            "/services/frost_video/student/cancel_request/" +
                course_date_id +
                "/" +
                user_id
        )
        .then((response) => {
            console.log("call Cancel request response:", response.data);
            return response.data;
        })
        .catch((error) => {
            throw new Error(error.message);
        });
};

export const sendCallAcceptedRequest = ({ course_date_id, user_id }) => {
    return apiClient
        .get(
            "/services/frost_video/student/accept_request/" +
                course_date_id +
                "/" +
                user_id
        )
        .then((response) => {
            console.log("call request response:", response.data);
            return response.data;
        })
        .catch((error) => {
            throw new Error(error.message);
        });
};

/**
 * @desc: Checks to see if the instructor has responed to the call request
 * @param course_date_id
 * @param user_id
 * @returns
 */
export const validateCallRequest = (
    course_date_id: number,
    user_id: number
) => {
    return useQuery<{
        success: boolean;
        message: string;
        call_request: boolean;
    }>(
        ["video-request", course_date_id, user_id],
        async () => {
            const response = await apiClient.get(
                `/services/frost_video/student/validate_request/${course_date_id}/${user_id}`
            );
            return {
                success: response.data.success,
                message: response.data.message,
                call_request: response.data.call_request,
            };
        },
        {
            cacheTime: 30000,
            staleTime: 30000,
            refetchOnMount: true,
            refetchOnWindowFocus: true,
            refetchInterval: 10000,
        }
    );
};

export const acceptCallRequest = ( course_date_id, user_id ) => {
    return apiClient
        .get(
            "/services/frost_video/student/accept_request/" +
                course_date_id +
                "/" +
                user_id
        )
        .then((response) => {
            console.log("call request response:", response.data);
            return response.data;
        })
        .catch((error) => {
            throw new Error(error.message);
        });
}

export const endCallRequest = ( course_date_id, user_id ) => {
    return apiClient
        .get(
            "/services/frost_video/student/end_call/" +
                course_date_id +
                "/" +
                user_id
        )
        .then((response) => {
            console.log("call request response:", response.data);
            return response.data;
        })
        .catch((error) => {
            throw new Error(error.message);
        });
}

