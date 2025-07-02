import React from "react";
import apiClient from "../../Config/axios";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
    StudentType,
    QueuedUserReturnType,
} from "../../Config/types";

/**
 * request a list of queued users that requested a video call
 * @returns
 */
export const getQueuedUsers = (courseDateId: number) => {
    const {
        data: queuedUsers,
        status,
        error,
    } = useQuery<QueuedUserReturnType>(
        ["queued-users", courseDateId],
        async () => {
            const response = await apiClient.get<QueuedUserReturnType>(
                `/services/frost_video/queues/${courseDateId}`
            );
            console.log("FETCH: Queuedusers: ", response);
            return response.data;
        },
        {
            cacheTime: 6000 * 30,
            staleTime: 30000,
            refetchOnMount: true,
            refetchOnWindowFocus: "always",
            refetchInterval: 30000,
        }
    );

    return { queuedUsers, status, error }; // Use queuedUsers instead of data
};

export interface ReturnType {
    data: listenCallReturnType;
    status: "error" | "success" | "loading";
    error: unknown;
}

export interface listenCallReturnType extends ReturnType {
    caller_id: number;
    success: boolean;
    message: string;
}

export const listenForCallAccepted = (
    courseDateId: number,
    userId: number
): ReturnType => {
    const { data, status, error } = useQuery<listenCallReturnType>(
        ["call-response", courseDateId, userId],
        async () => {
            const response = await apiClient.get<listenCallReturnType>(
                `/services/frost_video/listen_accept_call/${courseDateId}/${userId}`
            );
            console.log("Listen For Call: ", response);
            return response.data;
        },
        {
            cacheTime: 1000 * 30,
            staleTime: 10000,
            refetchOnMount: false,
            refetchOnWindowFocus: false,
            refetchInterval: 10000,
        }
        
    );

    return { data, status, error };
};

export const endCallRequest = async (courseDateId: number, userId: number) => {
    const response = await apiClient.post(`/services/frost_video/end_call/${courseDateId}/${userId}`, {
      course_date_id: courseDateId,
      user_id: userId,
    });
    console.log("End Call: ", response);
    return response.data;
  };
  
