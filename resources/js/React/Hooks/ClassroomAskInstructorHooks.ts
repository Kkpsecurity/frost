import apiClient from "../Config/axios";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";

export type ClassroomSessionMode = "TEACHING" | "Q&A" | "BREAK";

export type ClassroomSessionModeResponse = {
    success: boolean;
    mode: ClassroomSessionMode;
};

export type AskInstructorSubmitPayload = {
    course_date_id: number;
    topic: string;
    urgency: "Normal" | "Urgent";
    question: string;
};

export type AskInstructorSubmitResponse = {
    success: boolean;
    question_id?: number;
    message?: string;
};

export type AskInstructorQueueItem = {
    id: number;
    topic: string;
    urgency: "Normal" | "Urgent" | string;
    question: string;
    status: string;
    answer_visibility: "private" | "public" | null;
    answer_text: string | null;
    answered_at: string | null;
    ai_status: string | null;
    ai_answer_student: string | null;
    ai_sources: any;
    created_at: string | null;
};

export type AskInstructorMyQueueResponse = {
    success: boolean;
    questions: AskInstructorQueueItem[];
};

export function useClassroomSessionMode(courseDateId: number | null) {
    return useQuery({
        queryKey: ["classroom-session-mode", courseDateId],
        enabled: !!courseDateId,
        queryFn: async (): Promise<ClassroomSessionModeResponse> => {
            const response = await apiClient.get(
                `/classroom/session/mode?course_date_id=${courseDateId}`
            );
            return response.data;
        },
        gcTime: 30000,
        staleTime: 10000,
        refetchInterval: 10000,
    });
}

export function useAskInstructorMyQueue(courseDateId: number | null) {
    return useQuery({
        queryKey: ["ask-instructor-my", courseDateId],
        enabled: !!courseDateId,
        queryFn: async (): Promise<AskInstructorMyQueueResponse> => {
            const response = await apiClient.get(
                `/classroom/ask-instructor/my?course_date_id=${courseDateId}`
            );
            return response.data;
        },
        gcTime: 30000,
        staleTime: 10000,
        refetchInterval: 15000,
    });
}

export function useAskInstructorSubmit(courseDateId: number | null) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (payload: AskInstructorSubmitPayload): Promise<AskInstructorSubmitResponse> => {
            const response = await apiClient.post(`/classroom/ask-instructor`, payload);
            return response.data;
        },
        onSuccess: () => {
            if (courseDateId) {
                queryClient.invalidateQueries({ queryKey: ["ask-instructor-my", courseDateId] });
            }
        },
    });
}
