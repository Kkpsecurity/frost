import { useQuery } from "@tanstack/react-query";
import axios from "axios";
import { InstructorPollResponse, ClassroomPollResponse, ChatPollResponse } from "../types";

/**
 * Hook to poll instructor data every 30 seconds
 */
export const useInstructorDataPolling = () => {
    return useQuery({
        queryKey: ["instructor-data"],
        queryFn: async () => {
            console.log("📡 useInstructorDataPolling: EXECUTING queryFn - Fetching from /admin/instructors/instructor/data");
            try {
                const response = await axios.get<InstructorPollResponse>(
                    "/admin/instructors/instructor/data"
                );
                console.log("✅ Instructor data received:", response.data);
                return response.data;
            } catch (error) {
                console.error("❌ Error fetching instructor data:", error);
                throw error;
            }
        },
        refetchInterval: 5000, // Poll every 5 seconds for testing
        refetchOnWindowFocus: false,
        retry: 3,
        staleTime: 0, // Mark as stale immediately to force refetch
    });
};

/**
 * Hook to poll classroom data every 15 seconds (only when active)
 */
export const useClassroomDataPolling = (enabled: boolean = true) => {
    return useQuery({
        queryKey: ["classroom-data"],
        queryFn: async () => {
            console.log(
                "📡 Fetching classroom data from /admin/instructors/classroom/data"
            );
            const response = await axios.get<ClassroomPollResponse>(
                "/admin/instructors/classroom/data"
            );
            console.log("✅ Classroom data received:", response.data);
            console.log("🔍 courseDate field:", response.data.courseDate);
            console.log("🔍 courseDates field:", response.data.courseDates);
            return response.data;
        },
        refetchInterval: enabled ? 15000 : false, // Only poll when enabled
        refetchOnWindowFocus: false,
        retry: true,
        enabled: enabled, // Respect the enabled parameter
    });
};

/**
 * Hook to poll chat messages every 3 seconds (only when active)
 */
export const useChatMessagesPolling = (
    courseDateId: number | null | undefined,
    enabled: boolean = true,
) => {
    return useQuery({
        queryKey: ["chat-messages", courseDateId],
        queryFn: async () => {
            console.log(
                "📡 Fetching chat messages from /admin/instructors/classroom/chat",
            );
            const response = await axios.get<ChatPollResponse>(
                `/admin/instructors/classroom/chat?course_date_id=${courseDateId}`,
            );
            console.log("✅ Chat messages received:", response.data);
            return response.data;
        },
        refetchInterval: 3000, // Poll every 3 seconds
        refetchOnWindowFocus: false,
        retry: true,
        enabled: enabled && !!courseDateId, // Only poll if classroom is active and has a course date
    });
};
