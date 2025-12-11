/**
 * TypeScript types that match Laravel dashboard data structure
 * This ensures type safety between Laravel backend and React frontend
 */

export interface Student {
    id: number;
    fname: string;
    lname: string;
    email: string;
    // Add other student fields as needed
}

export interface CourseAuth {
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
    course?: {
        id: number;
        title: string;
        description?: string;
        slug: string;
    };
}

export interface Instructor {
    id: number;
    fname: string;
    lname: string;
    email: string;
    // Add other instructor fields as needed
}

export interface CourseDate {
    id: number;
    course_id: number;
    start_date: string;
    end_date: string;
    session_date?: string;
    // Add other course date fields as needed
}

/**
 * Lesson progress data from StudentDashboardService::getLessonsForCourse()
 */
export interface LessonProgressData {
    id: number;
    title: string;
    unit_id: number;
    unit_title: string;
    unit_ordering: number;
    credit_minutes: number;
    video_seconds: number;
    is_completed: boolean;
}

/**
 * Course lessons data with metadata
 */
export interface CourseAuthLessons {
    lessons: LessonProgressData[];
    modality: string; // 'self_paced' | 'instructor_led' | 'unknown'
    current_day_only: boolean;
    course_title: string;
}

/**
 * Lessons data structure - keyed by courseAuth.id
 */
export interface LessonsData {
    [courseAuthId: string]: CourseAuthLessons;
}

/**
 * Student-specific dashboard data (from student-props element)
 */
export interface StudentDashboardData {
    student: Student | null;
    course_auths: CourseAuth[];
    course_auth_id?: number | null;
    selected_course_auth_id?: number | null;
    lessons?: LessonsData;
    has_lessons?: boolean;
    validations?: any;
    student_attendance?: any | null;
    student_units?: any[]; // Array of StudentUnit records with created_at (arrival time)
}

/**
 * Class-specific dashboard data (from class-props element)
 */
export interface ClassDashboardData {
    instructor: Instructor | null;
    course_dates: CourseDate[];
    inst_unit?: any | null;
}

/**
 * Combined props data structure from the blade template
 */
export interface LaravelPropsData {
    courseAuthId: string | null;
    studentData: StudentDashboardData | null;
    classData: ClassDashboardData | null;
}

/**
 * Validation functions to ensure Laravel data matches expected structure
 */
export class LaravelPropsValidator {
    static validateStudent(student: any): student is Student {
        return (
            student &&
            typeof student.id === 'number' &&
            typeof student.fname === 'string' &&
            typeof student.lname === 'string' &&
            typeof student.email === 'string'
        );
    }

    static validateCourseAuth(auth: any): auth is CourseAuth {
        return (
            auth &&
            typeof auth.id === "number" &&
            typeof auth.course_id === "number" &&
            typeof auth.user_id === "number" &&
            typeof auth.is_passed === "boolean" &&
            typeof auth.id_override === "boolean" &&
            (typeof auth.created_at === "string" ||
                typeof auth.created_at === "number") &&
            (typeof auth.updated_at === "string" ||
                typeof auth.updated_at === "number")
        );
    }

    static validateInstructor(instructor: any): instructor is Instructor {
        return (
            instructor &&
            typeof instructor.id === 'number' &&
            typeof instructor.fname === 'string' &&
            typeof instructor.lname === 'string' &&
            typeof instructor.email === 'string'
        );
    }

    static validateCourseDate(courseDate: any): courseDate is CourseDate {
        return (
            courseDate &&
            typeof courseDate.id === 'number' &&
            typeof courseDate.course_id === 'number' &&
            typeof courseDate.start_date === 'string' &&
            typeof courseDate.end_date === 'string'
        );
    }

    static validateStudentDashboardData(data: any): data is StudentDashboardData {
        if (!data) {
            console.error('❌ Student dashboard data is null or undefined');
            return false;
        }

        // Validate student (can be null)
        if (data.student && !this.validateStudent(data.student)) {
            console.error('❌ Invalid student data:', data.student);
            return false;
        }

        // Validate course_auths array
        if (!Array.isArray(data.course_auths)) {
            console.error('❌ course_auths is not an array:', data.course_auths);
            return false;
        }

        // Validate each course auth if array is not empty
        for (const auth of data.course_auths) {
            if (!this.validateCourseAuth(auth)) {
                console.error('❌ Invalid course auth:', auth);
                return false;
            }
        }

        console.log('✅ Student dashboard data validation passed');
        return true;
    }

    static validateClassDashboardData(data: any): data is ClassDashboardData {
        if (!data) {
            console.error('❌ Class dashboard data is null or undefined');
            return false;
        }

        // Validate instructor (can be null)
        if (data.instructor && !this.validateInstructor(data.instructor)) {
            console.error('❌ Invalid instructor data:', data.instructor);
            return false;
        }

        // Validate course_dates array
        if (!Array.isArray(data.course_dates)) {
            console.error('❌ course_dates is not an array:', data.course_dates);
            return false;
        }

        // Validate each course date if array is not empty
        for (const courseDate of data.course_dates) {
            if (!this.validateCourseDate(courseDate)) {
                console.error('❌ Invalid course date:', courseDate);
                return false;
            }
        }

        console.log('✅ Class dashboard data validation passed');
        return true;
    }

    /**
     * Create safe default student dashboard data
     */
    static getDefaultStudentData(): StudentDashboardData {
        return {
            student: null,
            course_auths: []
        };
    }

    /**
     * Create safe default class dashboard data
     */
    static getDefaultClassData(): ClassDashboardData {
        return {
            instructor: null,
            course_dates: []
        };
    }
}
