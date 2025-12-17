/**
 * CourseDate Model
 * Represents a scheduled class session for a specific course unit on a specific date
 */

export interface CourseDate {
    id: number;
    course_unit_id: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
    classroom_created_at: string | null;
    classroom_metadata: any | null;
    
    // Computed/Relationship data from API
    course_name?: string;
    lesson_name?: string;
    lesson_count?: number; // Total lessons for this course unit
    module?: string;
    course_code?: string;
    time?: string;
    duration?: string;
    student_count?: number;
    class_status?: 'unassigned' | 'assigned' | 'in_progress' | 'completed' | null;
    
    // Instructor/Assistant data (populated when InstUnit exists)
    instructor_name?: string | null; // From InstUnit->created_by User fname + lname
    assistant_name?: string | null; // From InstUnit->assistant_id User fname + lname
    
    // Relationship data
    course_unit?: {
        id: number;
        title: string;
        course?: {
            id: number;
            title: string;
            code?: string;
        };
    };
    
    // InstUnit data (when instructor has started the class)
    inst_unit?: {
        id: number;
        created_by: number; // User ID of instructor who started the class
        assistant_id: number | null; // User ID of assigned assistant
        instructor: string | null; // Same as instructor_name (legacy compatibility)
        assistant: string | null; // Same as assistant_name (legacy compatibility)
        created_at: string; // When instructor took control of the class
        completed_at: string | null; // When class was marked as completed
        starts_at: string;
        ends_at: string;
    } | null;
    
    // Dynamic buttons based on class_status and user permissions
    buttons?: Record<string, string>;
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
