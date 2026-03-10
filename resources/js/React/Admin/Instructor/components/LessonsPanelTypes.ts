export interface Lesson {
    id: number;
    title: string;
    sort_order: number;
    lesson_type: string;
    is_completed: boolean;
    duration_minutes: number;
    description: string;
    content_url: string | null;
    objectives: string | null;
}

export interface InstLesson {
    id: number;
    lesson_id: number;
    created_at: string;
    completed_at: string | null;
    is_paused: boolean;
}

export interface LessonsPanelProps {
    courseDateId?: number;
    collapsed: boolean;
    onToggle: () => void;
    instUnit?: any;
    zoomReady?: boolean;
}
