/**
 * Video Quota Hook
 * Manages student video watch time quota
 */

import { useQuery } from '@tanstack/react-query';

export interface VideoQuotaData {
    total_hours: number;
    used_hours: number;
    remaining_hours: number;
    refunded_hours: number;
}

interface UseVideoQuotaResult {
    quota: VideoQuotaData | null;
    isLoading: boolean;
    error: Error | null;
    refetch: () => void;
}

const fetchVideoQuota = async (): Promise<VideoQuotaData> => {
    const response = await fetch('/classroom/video-quota', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data.data || data; // Handle both {data: {...}} and direct response
};

export const useVideoQuota = (): UseVideoQuotaResult => {
    const {
        data,
        isLoading,
        error,
        refetch
    } = useQuery({
        queryKey: ['video-quota'],
        queryFn: fetchVideoQuota,
        staleTime: 30 * 1000, // 30 seconds
        retry: 3,
        refetchOnWindowFocus: true, // Refresh when user returns to window
        refetchInterval: 60 * 1000, // Auto-refresh every 60 seconds
    });

    return {
        quota: data || null,
        isLoading,
        error: error as Error | null,
        refetch
    };
};
