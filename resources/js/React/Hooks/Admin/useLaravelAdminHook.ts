import { useQuery, UseQueryResult } from "@tanstack/react-query";
import apiClient from "../../Config/axios";
import { BaseLaravelShape, LaravelAdminShape } from "../../Config/types";

/**
 * Custom hook to fetch Laravel admin data.
 * Returns properly typed data.
 */
export const useLaravelAdminHook = (): UseQueryResult<BaseLaravelShape> => {
  return useQuery(["laravel-admin"], async () => {
    const { data } = await apiClient.get<BaseLaravelShape>("admin/frost/data/");
    
    if (!data) {
      throw new Error("No data received from API.");
    }
    
    console.log("LaravelAdminData", data);
    return data; // ✅ Now properly typed as LaravelAdminShape
  });
};
