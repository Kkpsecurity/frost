/**
 * Student Hooks Export
 * Centralized export for all student-related hooks
 */

export { useGetStudentData } from './useGetStudentData';
export { useGetClassData } from './useGetClassData';

// Re-export types for convenience
export type {
  Student,
  CourseAuth,
  Instructor,
  CourseDate,
  StudentData,
  ClassroomData,
  UseGetClassDataOptions,
  UseQueryResult
} from '../types/classroom';
