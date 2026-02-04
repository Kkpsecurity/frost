import apiClient from "../Config/axios";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";

export type ClassroomChatMessage = {
    id: number;
    user: {
        user_id: number;
        user_name: string;
        user_avatar: string | null;
        user_type: "student" | "instructor";
    };
    body: string;
    created_at: string | null;
};

export type ClassroomChatResponse = {
    success: boolean;
    enabled: boolean;
    messages: ClassroomChatMessage[];
};

export function useClassroomChat(courseDateId: number | null) {
    return useQuery({
        queryKey: ["classroom-chat", courseDateId],
        enabled: !!courseDateId,
        queryFn: async (): Promise<ClassroomChatResponse> => {
            const response = await apiClient.get(
                `/classroom/chat?course_date_id=${courseDateId}`
            );
            return response.data;
        },
        gcTime: 5 * 60 * 1000,
        staleTime: 2 * 1000,
        refetchInterval: 3000,
    });
}

export function usePostClassroomChatMessage(courseDateId: number | null) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (payload: { course_date_id: number; message: string }) => {
            const response = await apiClient.post(`/classroom/chat-messages`, payload);
            return response.data;
        },
        onSuccess: () => {
            if (courseDateId) {
                queryClient.invalidateQueries({ queryKey: ["classroom-chat", courseDateId] });
            }
        },
    });
}
