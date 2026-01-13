/**
 * Instructor Dashboard API Response Types
 *
 * These types define the exact structure of data returned from each polling endpoint.
 * Use these types in both React and backend to ensure synchronization.
 */

// ==========================================
// Poll 1: /classroom/instructor/data
// ==========================================

export interface InstructorPollResponse {
    instructor: InstructorData;
    instUnit: InstUnitData | null;
    instLessons: InstLessonData[];
    zoom: ZoomStatusData | null; // NEW: Zoom status managed by instructor poll
}

export interface InstructorData {
  id: number;
  fname: string;
  lname: string;
  email: string;
  avatar?: string | null;
  use_gravatar?: boolean;
  email_opt_in?: number; // Added to match InstructorType/UserType requirements
  is_active?: boolean;
  role_id?: number;
  email_verified_at?: string | null;
  created_at?: string;
  updated_at?: string;
  [key: string]: any;
}

export interface InstUnitData {
  id: number;
  course_date_id: number;
  created_by: number;
  assistant_id?: number | null;
  starts_at: string;
  ends_at: string;
  [key: string]: any;
}

export interface InstLessonData {
  id: number;
  inst_unit_id: number;
  course_unit_lesson_id: number;
  status: 'pending' | 'active' | 'completed';
  started_at?: string | null;
  ended_at?: string | null;
  [key: string]: any;
}

// Zoom Status Data (part of Instructor Poll)
export interface ZoomStatusData {
  status: 'enabled' | 'disabled' | 'error';
  is_active: boolean;
  email?: string;
  meeting_id?: string;
  course_name?: string;
  message?: string;
}

// ==========================================
// Poll 2: /classroom/data
// ==========================================

export interface ClassroomPollResponse {
  courseDates: CourseDateData[]; // Changed from courseDate (singular) to courseDates (plural array)
  courses: CourseData[];
  lessons: LessonData[];
  todayLessons?: LessonData[];
  upcomingLessons?: LessonData[];
  courseUnit?: CourseUnitData | null;
  courseLessons?: CourseLessonData[];
  students: StudentUnitData[];
  instUnit?: any | null; // Instructor unit for active session
  instLessons?: any[]; // Instructor lessons
}

export interface CourseDateData {
  id: number;
  course_id: number;
  course_unit_id: number;
  starts_at: string;
  ends_at: string;
  is_active: boolean;
  classroom_created_at?: string;
  [key: string]: any;
}

export interface CourseData {
  id: number;
  title: string;
  code?: string;
  [key: string]: any;
}

export interface LessonData {
  id: number;
  title: string;
  start_time: string;
  duration: number;
  status: string;
  [key: string]: any;
}

export interface CourseUnitData {
  id: number;
  course_id: number;
  title: string;
  description?: string;
  [key: string]: any;
}

export interface CourseLessonData {
  id: number;
  course_unit_id: number;
  title: string;
  lesson_order: number;
  duration: number;
  [key: string]: any;
}

export interface StudentUnitData {
  id: number;
  student_id: number;
  course_date_id: number;
  student_name?: string;
  student_email?: string;
  status: 'active' | 'completed' | 'dropped';
  [key: string]: any;
}

// ==========================================
// Poll 3: /classroom/chat
// ==========================================

export interface ChatPollResponse {
  messages: ChatMessageData[];
}

export interface ChatMessageData {
  id: number;
  sender_id: number;
  sender_name: string;
  sender_avatar?: string;
  course_date_id: number;
  message: string;
  sent_at: string;
  read_at?: string | null;
  [key: string]: any;
}

// ==========================================
// Merged Types (After Data Layer Merging)
// ==========================================

export interface MergedInstructorContextData extends InstructorPollResponse {}

export interface MergedClassroomContextData extends ClassroomPollResponse {}

export interface MergedChatContextData extends ChatPollResponse {}

// ==========================================
// Context Types
// ==========================================

export interface InstructorDataContextType {
    // Poll 1: Instructor Data
    instructorData: {
        instructor: any;
        instUnit: any;
        instLessons: any[];
    } | null;

    // Poll 2: Classroom Data
    classroomData: {
        courseDate: any;
        course: any;
        lessons: any[];
        courseDates: any[];
        courseUnit: any;
        instUnit: any;
        instLessons: any[];
        students: any[];
    } | null;

    // Poll 3: Chat Data
    chatData: {
        messages: any[];
    } | null;

    // Status
    isClassroomActive: boolean;
    isLoading: boolean;
    error: Error | null;
}
