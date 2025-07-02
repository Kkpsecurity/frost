import { useEffect, useState } from "react";

import apiClient from "../../Config/axios";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";

import { StudentType } from "../../Config/types";
import { type } from "os";

let debug = false;

export const useSupportCenterHook = (studentId) => {
    const { data, status, error } = useQuery(
        ["course", studentId], 
        async () => {
            const response = await apiClient.get(
                `/admin/frost-support/dashboard/get-student-data/${studentId}`
            );
            console.log("useActiveCourseData: ", response);
            return response.data;
        },
        {
            cacheTime: 60000 * 30, // 30 minutes
            staleTime: 60000 * 5, // 5 minutes
            refetchOnMount: true,
            refetchOnWindowFocus: "always",
            refetchInterval: 60000, // 1 minute, adjust as needed
            enabled: !!studentId, // Query will only run if studentId is truthy
        }
    );

    console.log("useActiveCourseDataDatData: ", data);

    return { data, status, error };
};

