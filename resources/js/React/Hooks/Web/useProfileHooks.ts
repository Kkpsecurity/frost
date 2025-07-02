import { useQuery } from "@tanstack/react-query";
import apiClient from "../../Config/axios";
import { ProfileDataShape } from "../../Config/types";

export const getProfileUser = (): {
  data: ProfileDataShape | undefined;
  isLoading: boolean;
  error: any;
} => {
  const { data, isLoading, error } = useQuery<ProfileDataShape>(
    ["ProfileData"],
    async () => {
      const { data } = await apiClient.get<ProfileDataShape>(`account/profile/data/`);
      return data;
    }
  );

  return { data, isLoading, error };
};
