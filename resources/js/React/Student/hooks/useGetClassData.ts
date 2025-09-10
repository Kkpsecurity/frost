/**
 * Class Data Hook
 * Fetches classroom data including instructors and course dates
 * Filters by date and live status when specified
 */

import { useQuery } from '@tanstack/react-query';
import type { ClassroomData, UseGetClassDataOptions, UseQueryResult } from '../types/classroom';

const fetchClassData = async (
  classId: string | number,
  options: UseGetClassDataOptions = {}
): Promise<ClassroomData> => {
  const searchParams = new URLSearchParams();

  if (options.date) {
    searchParams.append('date', options.date);
  }

  if (options.isLive !== undefined) {
    searchParams.append('isLive', options.isLive.toString());
  }

  const queryString = searchParams.toString();
  const url = `/classroom/debug/class${queryString ? `?${queryString}` : ''}`;

  const response = await fetch(url, {
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

  return response.json();
};

export const useGetClassData = (
  classId: string | number,
  options: UseGetClassDataOptions = {}
): UseQueryResult<ClassroomData> => {
  const {
    data,
    isLoading,
    error,
    refetch
  } = useQuery({
    queryKey: ['class', classId, options.date, options.isLive],
    queryFn: () => fetchClassData(classId, options),
    staleTime: 60 * 1000, // 60 seconds
    retry: 3,
    refetchOnWindowFocus: false,
    // Only run query if classId is provided
    enabled: !!classId
  });

  return {
    data,
    isLoading,
    error: error as Error | null,
    refetch
  };
};
