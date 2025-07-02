import { useQuery } from "@tanstack/react-query";
import apiClient from "../../Config/axios";
import { LaravelDataShape } from "../../Config/types";

export const useLaravelData = (course_auth_id: string) => {
    return useQuery<LaravelDataShape>(
        ["laravelData", course_auth_id],
        async () => {
            const { data } = await apiClient.get(`/frost/data/${course_auth_id}`);
            return data;
        },
        {
            staleTime: 1000 * 60 * 60, // Data is fresh for 1 hour
            cacheTime: 1000 * 60 * 60, // Data is cached for 1 hour
            refetchOnWindowFocus: true, // Refetch on window focus
            refetchInterval: 15000, // Poll every 15 seconds
        }
    );
};
