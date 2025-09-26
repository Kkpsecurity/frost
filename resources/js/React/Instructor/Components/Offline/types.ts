// Shared types for offline instructor components

// Shared types for offline instructor components

export interface CourseDate {
    id: number;
    course_name: string;
    lesson_name: string;
    lesson_count: number; // Total lessons for this course unit
    module: string;
    course_code: string;
    time: string;
    duration: string;
    student_count: number;
    class_status: string; // "unassigned" | "assigned" | "completed" | "expired"
    // NOTE: These instructor/assistant names are only populated when InstUnit exists
    // They are constructed from User.fname + User.lname by the backend service
    instructor_name: string | null; // From InstUnit->created_by User fname + lname
    assistant_name: string | null; // From InstUnit->assistant_id User fname + lname
    starts_at: string;
    ends_at: string;
    buttons?: Record<string, string>; // Dynamic buttons based on class_status and user permissions
    // WORKFLOW: CourseDate exists for scheduled class → Instructor takes control → InstUnit created
    inst_unit?: {
        id: number;
        instructor: string | null; // Same as instructor_name (legacy compatibility)
        assistant: string | null; // Same as assistant_name (legacy compatibility)
        created_at: string; // When instructor took control of the class
        completed_at: string | null; // When class was marked as completed
    } | null; // null = no instructor has taken control yet
}

export interface AssignmentHistoryRecord {
    course_date_id: number;
    date: string;
    time: string;
    course_name: string;
    unit_name: string;
    unit_code: string;
    day_number: string;
    assignment_status: "assigned" | "unassigned" | "completed";
    status_color: string;
    instructor: string | null;
    assistant: string | null;
    assigned_at: string | null;
    completed_at: string | null;
    duration: string;
    inst_unit_id: number | null;
}

export interface BulletinBoardData {
    lessons: CourseDate[];
    message: string;
    has_lessons: boolean;
    assignment_history: AssignmentHistoryRecord[];
    metadata: {
        date: string;
        count: number;
        generated_at: string;
    };
}

export interface InstructorDashboardProps {
    mode?: "offline" | "online";
    courseData?: any;
    studentData?: any[];
    lessonsData?: any[];
}
