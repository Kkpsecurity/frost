import { useState, useEffect } from 'react';

interface ClassAttendanceCheck {
    loading: boolean;
    hasActiveClass: boolean;
    courseDate?: any;
    studentUnit?: any;
    onboardingUrl?: string;
    message?: string;
    error?: string;
}

/**
 * Hook to check if student has an active class today and needs to mark attendance
 */
export const useClassAttendance = () => {
    const [state, setState] = useState<ClassAttendanceCheck>({
        loading: true,
        hasActiveClass: false
    });

    useEffect(() => {
        const checkClassAttendance = async () => {
            try {
                console.log('🔍 Checking for active class requiring attendance...');

                const response = await fetch('/classroom/check-attendance', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('📋 Class attendance check response:', data);

                    setState({
                        loading: false,
                        hasActiveClass: data.attendance_required || false,
                        courseDate: data.course_date,
                        studentUnit: data.student_unit_id ? { id: data.student_unit_id } : null,
                        onboardingUrl: data.attendance_url,
                        message: data.message
                    });

                    // If active class found and attendance needed, redirect immediately
                    if (data.attendance_required && data.attendance_url) {
                        console.log('🚀 Active class found - redirecting to attendance:', data.attendance_url);
                        window.location.href = data.attendance_url;
                        return;
                    }

                } else {
                    console.log('❌ Class attendance check failed:', response.status);
                    setState({
                        loading: false,
                        hasActiveClass: false,
                        error: `HTTP ${response.status}`
                    });
                }

            } catch (error) {
                console.error('💥 Error checking class attendance:', error);
                setState({
                    loading: false,
                    hasActiveClass: false,
                    error: error instanceof Error ? error.message : 'Unknown error'
                });
            }
        };

        checkClassAttendance();
    }, []);

    return state;
};
