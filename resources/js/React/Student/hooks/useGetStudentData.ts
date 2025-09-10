/**
 * Student Data Hook
 * Fetches student profile and course authorization data
 */

import { useQuery } from '@tanstack/react-query';
import type { StudentData, UseQueryResult } from '../types/classroom';

const fetchStudentData = async (): Promise<StudentData> => {
  const response = await fetch('/classroom/debug/student', {
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

export const useGetStudentData = (): UseQueryResult<StudentData> => {
  const {
    data,
    isLoading,
    error,
    refetch
  } = useQuery({
    queryKey: ['student', 'me'],
    queryFn: fetchStudentData,
    staleTime: 60 * 1000, // 60 seconds
    retry: 3,
    refetchOnWindowFocus: false
  });

  return {
    data,
    isLoading,
    error: error as Error | null,
    refetch
  };
};
