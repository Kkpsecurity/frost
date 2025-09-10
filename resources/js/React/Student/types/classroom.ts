/**
 * Classroom Types - Derived from Backend Models
 * These match the exact structure returned by the debug endpoints
 */

// User model fields (from User.php fillable)
export interface Student {
  id: number;
  fname: string | null;
  lname: string | null;
  name: string | null;
  email: string;
  avatar: string | null;
  use_gravatar: boolean;
  student_info: any | null;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  class: string;
}

// CourseAuth model fields (from migration)
export interface CourseAuth {
  id: number;
  user_id: number;
  course_id: number;
  created_at: string;
  updated_at: string;
  agreed_at: string | null;
  completed_at: string | null;
  is_passed: boolean;
  start_date: string | null;
  expire_date: string | null;
  disabled_at: string | null;
  disabled_reason: string | null;
  submitted_at: string | null;
  submitted_by: number | null;
  dol_tracking: string | null;
  exam_admin_id: number | null;
  range_date_id: number | null;
  id_override: boolean;
}

// Instructor data structure
export interface Instructor {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  bio: string | null;
  certifications: string[];
  profile_image: string | null;
  specialties: string[];
  rating: number | null;
  total_courses: number | null;
  years_experience: number | null;
}

// CourseDate data structure
export interface CourseDate {
  id: number;
  course_id: number;
  instructor_id: number;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  timezone: string;
  location: string;
  status: string;
  max_students: number;
  current_enrollment: number;
  meeting_link: string | null;
  course_title: string;
  created_at: string;
  updated_at: string;
}

// API Response Types
export interface StudentData {
  student: Student;
  courseAuth: CourseAuth[];
}

export interface ClassroomData {
  instructors: Instructor[];
  courseDates: CourseDate[];
}

// Hook Options
export interface UseGetClassDataOptions {
  date?: string;
  isLive?: boolean;
}

// Hook Return Types
export interface UseQueryResult<T> {
  data: T | undefined;
  isLoading: boolean;
  error: Error | null;
  refetch: () => void;
}
