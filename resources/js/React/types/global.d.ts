// Global type definitions for the Frost application

// User and Authentication Types
export interface User {
    id: number;
    fname: string;
    lname: string;
    email: string;
    role: string;
    created_at: string;
    updated_at: string;
}

export interface Instructor extends User {
    instructor_id?: number;
    specializations?: string[];
    qualifications?: string[];
}

export interface Student extends User {
    student_id: string;
    enrollment_date?: string;
    status: "active" | "inactive" | "suspended";
}

// Course Related Types
export interface Course {
    id: number;
    title: string;
    title_long?: string;
    price: number;
    total_minutes: number;
    is_active: boolean;
    needs_range: boolean;
    course_type: "D" | "G";
    description?: string;
    created_at: string;
    updated_at: string;
}

export interface CourseUnit {
    id: number;
    course_id: number;
    title: string;
    admin_title?: string;
    ordering: number;
    course: Course;
}

export interface Lesson {
    id: number;
    title: string;
    credit_minutes: number;
    video_seconds: number;
    duration_minutes?: number;
}

// Course Date and Scheduling Types
export interface CourseDate {
    id: number;
    course_id: number;
    course_unit_id: number;
    instructor_id: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
    student_count: number;
    notes?: string;
    course: Course;
    course_unit: CourseUnit;
    students: Student[];
    attendance_records: AttendanceRecord[];
}

// Attendance Management Types
export interface AttendanceRecord {
    id: number;
    course_date_id: number;
    student_id: number;
    status: "present" | "absent" | "late" | "excused";
    marked_at: string;
    marked_by_instructor_id: number;
    notes?: string;
    student: Student;
}

// Bulletin Board Types
export interface Announcement {
    id: number;
    title: string;
    content: string;
    type: "general" | "urgent" | "training" | "policy";
    author: string;
    created_at: string;
    expires_at?: string;
}

export interface InstructorResource {
    id: number;
    title: string;
    description: string;
    type: "document" | "video" | "link" | "training";
    url: string;
    category: string;
    created_at: string;
}

export interface BulletinBoardData {
    announcements: Announcement[];
    available_courses: Course[];
    instructor_resources: InstructorResource[];
    quick_stats: {
        total_instructors: number;
        active_courses_today: number;
        students_in_system: number;
    };
}

// Dashboard State Types
export interface DashboardState {
    hasScheduledCourses: boolean;
    activeCourses: CourseDate[];
    upcomingCourses: CourseDate[];
    recentCourses: CourseDate[];
    bulletinContent: BulletinBoardData;
    instructorProfile: Instructor;
}

// API Response Types
export interface ApiResponse<T> {
    data: T;
    message?: string;
    status: "success" | "error";
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

// Component Props Types
export interface ComponentProps {
    className?: string;
    children?: React.ReactNode;
}

// Query Key Types for React Query
export interface QueryKeys {
    instructor: {
        dashboard: () => ["instructor", "dashboard"];
        courseDates: () => ["instructor", "course-dates"];
        courseDate: (id: number) => ["instructor", "course-date", number];
        students: (courseDateId: number) => ["instructor", "students", number];
        bulletinBoard: () => ["instructor", "bulletin-board"];
        announcements: () => ["instructor", "announcements"];
        resources: () => ["instructor", "resources"];
        stats: () => ["instructor", "stats"];
        upcomingClasses: () => ["instructor", "upcoming-classes"];
    };
}

// Form Types
export interface AttendanceFormData {
    attendance: Record<number, "present" | "absent" | "late" | "excused">;
    notes?: Record<number, string>;
}

export interface LessonProgressData {
    lesson_id: number;
    course_date_id: number;
    completed: boolean;
    notes?: string;
    completion_time?: string;
}

// Error Types
export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    status: number;
}

// Utility Types
export type LoadingState = "idle" | "loading" | "success" | "error";

export interface LoadingStates {
    dashboard: LoadingState;
    courseDates: LoadingState;
    attendance: LoadingState;
    bulletinBoard: LoadingState;
}

declare global {
    interface Window {
        // Laravel data injected into pages
        user?: User;
        csrfToken?: string;
        appUrl?: string;

        // React component rendering functions
        InstructorComponents: any;
        renderInstructorComponent: (
            componentName: string,
            containerId: string,
            props?: any
        ) => void;
    }
}

export {};
