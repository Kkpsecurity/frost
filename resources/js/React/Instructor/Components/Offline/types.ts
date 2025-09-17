// Shared types for offline instructor components

export interface CourseDate {
    id: number;
    course_name: string;
    class_day: string;
    lesson_count: number;
    calendar_title: string;
    instructor: string;
    start_time: string;
    end_time: string;
    student_count: number;
    is_scheduled: boolean;
    is_live: boolean;
    status: string;
}

export interface InstructorDashboardProps {
    mode?: "offline" | "online";
    courseData?: any;
    studentData?: any[];
    lessonsData?: any[];
}
