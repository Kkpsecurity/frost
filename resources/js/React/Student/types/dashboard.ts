// Dashboard Types

export interface User {
    id: number;
    fname: string;
    lname: string;
    email: string;
}

export interface CourseAuth {
    id: number;
    course_id: number;
    user_id: number;
    expire_date?: string;
    completed_at?: string;
    disabled_at?: string;
    course?: {
        id: number;
        title: string;
        description?: string;
    };
}

export interface DashboardStats {
    total_courses: number;
    active_courses: number;
    completed_courses: number;
    overall_progress: number;
}

export interface DashboardData {
    user: User;
    incompleteAuths: CourseAuth[];
    completedAuths: CourseAuth[];
    mergedAuths: CourseAuth[];
    stats: DashboardStats;
}
