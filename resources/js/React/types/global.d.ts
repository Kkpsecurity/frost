// Global type declarations for Laravel application

import { AxiosStatic } from 'axios';

declare global {
    interface Window {
        axios: AxiosStatic;
        Pusher?: any;
        Echo?: any;
    }
}

// Application Types
export interface Instructor {
    id: number;
    name: string;
    fname: string; // First name for display
    email: string;
    role: string;
    permissions?: string[];
}

export interface Announcement {
    id: number;
    title: string;
    content: string;
    priority: "high" | "medium" | "low";
    type: string;
    author: string;
    created_at: string;
    expires_at?: string;
}

export interface InstructorResource {
    id: number;
    title: string;
    type: string;
    category: string;
    url: string;
    description?: string;
}

export interface Course {
    id: number;
    title: string;
    description?: string;
    total_minutes: number;
    price: number;
    is_active: boolean;
}

export interface CourseUnit {
    id: number;
    title: string;
}

export interface BulletinBoardData {
    announcements: Announcement[];
    instructor_resources: InstructorResource[];
    available_courses: CourseDate[];
    quick_stats: any;
    reminders: string[];
}

export interface CourseDate {
    id: number;
    course_name: string;
    title: string;
    description?: string;
    total_minutes: number;
    price: number;
    is_active: boolean;
    start_date: string;
    starts_at: string; // Alternative property name
    end_date: string;
    ends_at: string; // Alternative property name
    instructor: string;
    students_enrolled: number;
    student_count: number; // Alternative property name
    status: "active" | "upcoming" | "completed";
    course: Course;
    course_unit: CourseUnit;
    attendance_records?: any[];
    notes?: string;
}

export {};
