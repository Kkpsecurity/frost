import React from 'react';
import { createRoot } from 'react-dom/client';
import StudentClassroom from './Components/StudentClassroom';

// Lesson interface
interface Lesson {
    id: number;
    title: string;
    unit_id: number;
    unit_title: string;
    unit_ordering: number;
    credit_minutes: number;
    video_seconds: number;
}

// Props interface matching the Laravel blade template
interface StudentClassroomProps {
    student: {
        id: number;
        fname: string;
        lname: string;
        email: string;
    };
    courseAuth: {
        id: number;
        course_id: number;
        user_id: number;
        created_at: string | number;
        updated_at: string | number;
        agreed_at?: string | number | null;
        completed_at?: string | number | null;
        is_passed: boolean;
        start_date?: string | null;
        expire_date?: string | null;
        disabled_at?: string | number | null;
        disabled_reason?: string | null;
        submitted_at?: string | number | null;
        submitted_by?: number | null;
        dol_tracking?: string | null;
        exam_admin_id?: number | null;
        range_date_id?: number | null;
        id_override: boolean;
        progress?: number;
    };
    course: {
        id: number;
        title: string;
        description?: string;
        slug: string;
    };
    lessons: Lesson[];
    modality: 'online' | 'in_person' | 'offline' | 'unknown';
    current_day_only: boolean;
}

// Initialize React app for Student Classroom
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('student-classroom-container');
    const propsScript = document.getElementById('student-classroom-props');

    if (container && propsScript) {
        try {
            const props: StudentClassroomProps = JSON.parse(propsScript.textContent || '{}');

            console.log('🏫 StudentClassroom: Initializing with props:', props);

            // Validate required props
            if (!props.student || !props.courseAuth || !props.course) {
                console.error('❌ StudentClassroom: Missing required props', {
                    hasStudent: !!props.student,
                    hasCourseAuth: !!props.courseAuth,
                    hasCourse: !!props.course,
                    hasLessons: !!props.lessons,
                    lessonsCount: props.lessons?.length || 0,
                    modality: props.modality,
                    currentDayOnly: props.current_day_only
                });

                // Show error message in container
                container.innerHTML = `
                    <div class="container-lg py-5">
                        <div class="row justify-content-center">
                            <div class="col-md-8 text-center">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Error:</strong> Unable to load classroom data. Please try again.
                                </div>
                                <a href="/classroom" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            const root = createRoot(container);
            root.render(
                <StudentClassroom
                    student={props.student}
                    courseAuth={props.courseAuth}
                    course={props.course}
                    lessons={props.lessons || []}
                    modality={props.modality || 'unknown'}
                    current_day_only={props.current_day_only || false}
                />
            );

            console.log('✅ StudentClassroom: React component mounted successfully');

        } catch (error) {
            console.error('❌ StudentClassroom: Error parsing props or mounting component:', error);

            // Show error message
            container.innerHTML = `
                <div class="container-lg py-5">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> Failed to initialize classroom. Please refresh the page.
                            </div>
                            <button onclick="window.location.reload()" class="btn btn-outline-primary me-2">
                                <i class="fas fa-refresh me-2"></i>
                                Refresh Page
                            </button>
                            <a href="/classroom" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }
    } else {
        console.error('❌ StudentClassroom: Container or props not found', {
            hasContainer: !!container,
            hasPropsScript: !!propsScript
        });
    }
});
