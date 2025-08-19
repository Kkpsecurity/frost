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
    email: string;
    role: string;
    permissions?: string[];
}

export interface Announcement {
    id: number;
    title: string;
    content: string;
    priority: 'high' | 'medium' | 'low';
    created_at: string;
}

export interface InstructorResource {
    id: number;
    title: string;
    type: string;
    url: string;
    description?: string;
}

export interface BulletinBoardData {
    announcements: Announcement[];
    instructor_resources: InstructorResource[];
    available_courses: CourseDate[];
    reminders: string[];
}

export interface CourseDate {
    id: number;
    course_name: string;
    start_date: string;
    end_date: string;
    instructor: string;
    students_enrolled: number;
    status: 'active' | 'upcoming' | 'completed';
}

export {};
