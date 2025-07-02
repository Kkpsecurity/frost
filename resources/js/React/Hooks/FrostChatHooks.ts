import apiClient from "../Config/axios";
import { useQuery, useQueryClient, useMutation } from "@tanstack/react-query";
import {
    StudentType,
    ReturnChatMessageType,
    FrostMessage,
} from "../Config/types"; // Import StudentType

interface FrostChatData {
    chatMessages?: ReturnChatMessageType[];
    isLoading: boolean;
    isError?: boolean;
    error?: {};
}

export const getFrostMessages = (course_date_id: string, user_id: string): FrostChatData => {
    const {
        data: chatMessages,
        isLoading,
        isError,
        error,
    } = useQuery(
        ["chatroom", course_date_id],
        async () => {
            const response = await apiClient.get(
                `/services/chat/messages/${course_date_id}/${user_id}`
            );
            return response.data;
        },
        {
            cacheTime: 30000, // cache for 30 seconds
            staleTime: 30000, // allow stale data for 30 seconds
            refetchInterval: 15000, // poll every 15 seconds
        }
    );

    return { chatMessages, isLoading, isError, error };
};


export const postMessage = (chat: {
    user_id: number;
    message: string;
    course_date_id: string;
    user_type: string;
}) => {
    return apiClient
        .post(`/services/chat/messages/${chat.course_date_id}/${chat.user_id}`, {
            message: chat.message,
            user_id: chat.user_id,
            course_date_id: chat.course_date_id,
            user_type: chat.user_type,
        })
        .then((response) => response.data)
        .catch((error) => {
            throw new Error(error.message);
        });
};

export const postFrostMessage = (course_date_id: string, user_id: string) => {
    const queryClient = useQueryClient();
    return useMutation(postMessage, {
        onSuccess: () => {
            queryClient.invalidateQueries(["chatroom", course_date_id]);
        },
    });
};

export const enableFrostChat = (course_date_id: string) => {
    return apiClient
        .get(`/services/chat/enable/${course_date_id}`)
        .then((response) => response.data)
        .catch((error) => {
            throw new Error(error.message);
        });
};
