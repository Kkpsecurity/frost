import { useQuery } from "@tanstack/react-query";
import { UserData, SessionValidationResponse } from "./userTypes";

/**
 * Hook to get current user data and role information
 */
export const useUser = () => {
    const { data, isLoading, error } = useQuery<SessionValidationResponse>({
        queryKey: ["userSession"],
        queryFn: async () => {
            const response = await fetch("/admin/instructors/validate", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
                },
            });

            if (!response.ok) {
                throw new Error("Failed to validate session");
            }

            return response.json();
        },
        staleTime: 1000 * 60 * 5, // 5 minutes
        retry: 1,
    });

    const user: UserData | null = data?.instructor || null;
    const isAuthenticated: boolean = data?.authenticated || false;
    const isSysAdmin: boolean = user?.is_sys_admin || false;

    return {
        user,
        isAuthenticated,
        isSysAdmin,
        isLoading,
        error,
    };
};
