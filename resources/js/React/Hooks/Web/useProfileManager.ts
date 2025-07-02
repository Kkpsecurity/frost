import { useMutation } from "@tanstack/react-query";
import apiClient from "../../Config/axios";

export const useUpdateProfile = () => {
    return useMutation<UpdateProfileResponse, Error, UpdateProfileRequest>(
        updateProfile,
        {
            onError: (error) => {
                // handle error here
            },
        }
    );
};

export const useUpdatePassword = () => {
    return useMutation<UpdatePasswordResponse, Error, UpdatePasswordRequest>(
        updatePassword,
        {
            onError: (error) => {
                // handle error here
            },
        }
    );
};

// Update Profile

interface UpdateProfileRequest {
    user_id: number;
    fname: string;
    lname: string;
    email: string;
}

interface UpdateProfileResponse {
    // define response data type here
}

export const updateProfile = async (
    profile: UpdateProfileRequest
): Promise<UpdateProfileResponse> => {
    const url = `/account/profile/update/${profile.user_id}`;
    const response = await apiClient.post(url, profile);
    return response.data;
};

interface UpdatePasswordRequest {
    user_id: number;
    old_password: string;
    password: string;
    password_confirmation: string;
}

interface UpdatePasswordResponse {
    // define response data type here
}

export const updatePassword = async (
    password: UpdatePasswordRequest
): Promise<UpdatePasswordResponse> => {
    const url = `/account/password/update/${password.user_id}`;
    const response = await apiClient.post(url, password);
    return response.data;
};
