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
    } = useQuery({
        queryKey: ["chatroom", course_date_id],
        queryFn: async () => {
            try {
                const response = await apiClient.get(
                    `/admin/instructors/classroom/chat?course_date_id=${course_date_id}&user_id=${user_id}`
                );
                return response.data;
            } catch (error: any) {
                // If the endpoint doesn't exist yet, return mock data for development
                if (error.response?.status === 404) {
                    console.warn("Chat endpoint not implemented yet, using mock data");
                    return [];
                }
                throw error;
            }
        },
        gcTime: 30000, // cache for 30 seconds (renamed from cacheTime)
        staleTime: 30000, // allow stale data for 30 seconds
        refetchInterval: 15000, // poll every 15 seconds
    });

    return { chatMessages, isLoading, isError, error };
};


export const postMessage = (chat: {
    user_id: number;
    message: string;
    course_date_id: string;
    user_type: string;
}) => {
    return apiClient
        .post(`/admin/instructors/chat-messages`, {
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
    return useMutation({
        mutationFn: postMessage,
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["chatroom", course_date_id] });
        },
    });
};

export const enableFrostChat = (course_date_id: string) => {
    return apiClient
        .post(`/admin/instructors/chat-enable`, {
            course_date_id: course_date_id,
        })
        .then((response) => response.data)
        .catch((error) => {
            throw new Error(error.message);
        });
};
