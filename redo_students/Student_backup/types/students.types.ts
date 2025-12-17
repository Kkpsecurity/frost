/**
 * Student Domain Types & Aliases
 * 
 * Following React Types & Interfaces Rules:
 * - type = single-dimension props or atomic aliases
 * - interface = multi-field shapes (domain models)
 * - Domain entities end with Type
 * - Props end with Props  
 * - Shapes end with Shape
 */

// =============================================================================
// ATOMIC ALIASES (type)
// =============================================================================

export type StudentId = number;
export type UserId = number;
export type CourseId = number;
export type RoleId = number;
export type EmailAddress = string;
export type DateTimeString = string;
export type CourseStatus = 'not_started' | 'in_progress' | 'completed' | 'expired' | 'failed';
export type UserRoleName = 'admin' | 'instructor' | 'coordinator' | 'assistant' | 'student';
export type DashboardView = 'ONLINE' | 'OFFLINE';
export type CourseCategory = 'D' | 'G';
export type ActivityType = 'lesson_started' | 'lesson_completed' | 'unit_completed' | 'exam_taken' | 'course_completed';

// =============================================================================
// DOMAIN MODELS (interface)
// =============================================================================

/**
 * User Domain Model
 * References: App\Models\User
 * Table: users
 */
export interface UserType {
    id: UserId;
    fname: string;
    lname: string;
    email: EmailAddress;
    is_active: boolean;
    role_id: RoleId;
    avatar?: string;
    use_gravatar: boolean;
    student_info?: Record<string, any>;
    email_opt_in: boolean;
    created_at: DateTimeString;
    updated_at: DateTimeString;
    email_verified_at?: DateTimeString;
    
    // Virtual/computed properties
    name?: string;
    fullname?: string;
}

/**
 * Student Domain Model (extends UserType for student-specific context)
 */
export interface StudentType extends UserType {
    role_id: 5; // Student role ID
    course_auths?: CourseAuthType[];
    student_units?: StudentUnitType[];
}

/**
 * Instructor Domain Model (extends UserType for instructor-specific context)
 */
export interface InstructorType extends UserType {
    role_id: 1 | 2 | 3 | 4; // Admin, Instructor, or other roles
}

/**
 * Role Domain Model
 * References: App\Models\Role
 * Table: roles
 */
export interface RoleType {
    id: RoleId;
    name: string;
}

// =============================================================================
// COURSE RELATED TYPES
// =============================================================================

/**
 * Course Domain Model
 * References: App\Models\Course
 * Table: courses
 */
export interface CourseType {
    id: CourseId;
    title: string;
    title_long?: string;
    description?: string;
    price: number;
    total_minutes: number;
    policy_expire_days: number;
    is_active: boolean;
    exam_id?: number;
    eq_spec_id?: number;
    zoom_creds_id?: number;
    needs_range: boolean;
    dates_template?: Record<string, any>;
    
    // Virtual/computed properties
    course_type?: CourseCategory;
    duration_days?: number;
    frequency_type?: 'weekly' | 'biweekly';
}

/**
 * CourseAuth Domain Model (Course Authorization/Purchase)
 * References: App\Models\CourseAuth
 * Table: course_auths
 */
export interface CourseAuthType {
    id: number;
    user_id: UserId;
    course_id: CourseId;
    created_at: DateTimeString;
    updated_at: DateTimeString;
    agreed_at?: DateTimeString;
    completed_at?: DateTimeString;
    is_passed: boolean;
    start_date?: DateTimeString;
    expire_date?: DateTimeString;
    disabled_at?: DateTimeString;
    disabled_reason?: string;
    submitted_at?: DateTimeString;
    submitted_by?: UserId;
    dol_tracking?: string;
    exam_admin_id?: UserId;
    range_date_id?: number;
    id_override: boolean;
    
    // Relationships
    course?: CourseType;
    user?: UserType;
    student_units?: StudentUnitType[];
    exam_auths?: ExamAuthType[];
    
    // Virtual/computed properties
    progress?: number;
    status?: CourseStatus;
    is_active?: boolean;
    is_expired?: boolean;
    is_failed?: boolean;
}

/**
 * CourseUnit Domain Model
 * References: App\Models\CourseUnit
 * Table: course_units
 */
export interface CourseUnitType {
    id: number;
    course_id: CourseId;
    title: string;
    description?: string;
    sort_order: number;
    is_active: boolean;
    
    // Relationships
    course?: CourseType;
    course_unit_lessons?: CourseUnitLessonType[];
}

/**
 * CourseUnitLesson Domain Model
 * References: App\Models\CourseUnitLesson
 * Table: course_unit_lessons
 */
export interface CourseUnitLessonType {
    id: number;
    course_unit_id: number;
    title: string;
    content?: string;
    sort_order: number;
    is_active: boolean;
    
    // Relationships
    course_unit?: CourseUnitType;
}

// =============================================================================
// STUDENT PROGRESS TYPES
// =============================================================================

/**
 * StudentUnit Domain Model
 * References: App\Models\StudentUnit
 * Table: student_units
 */
export interface StudentUnitType {
    id: number;
    course_auth_id: number;
    course_unit_id: number;
    started_at?: DateTimeString;
    completed_at?: DateTimeString;
    is_completed: boolean;
    
    // Relationships
    course_auth?: CourseAuthType;
    course_unit?: CourseUnitType;
    student_lessons?: StudentLessonType[];
}

/**
 * StudentLesson Domain Model
 * References: App\Models\StudentLesson
 * Table: student_lessons
 */
export interface StudentLessonType {
    id: number;
    student_unit_id: number;
    course_unit_lesson_id: number;
    started_at?: DateTimeString;
    completed_at?: DateTimeString;
    is_completed: boolean;
    time_spent?: number; // in minutes
    
    // Relationships
    student_unit?: StudentUnitType;
    course_unit_lesson?: CourseUnitLessonType;
}

// =============================================================================
// EXAM TYPES
// =============================================================================

/**
 * ExamAuth Domain Model
 * References: App\Models\ExamAuth
 * Table: exam_auths
 */
export interface ExamAuthType {
    id: number;
    course_auth_id: number;
    exam_id: number;
    started_at?: DateTimeString;
    completed_at?: DateTimeString;
    score?: number;
    is_passed: boolean;
    attempts: number;
    max_attempts: number;
    
    // Relationships
    course_auth?: CourseAuthType;
    exam?: ExamType;
}

/**
 * Exam Domain Model
 * References: App\Models\Exam
 * Table: exams
 */
export interface ExamType {
    id: number;
    title: string;
    description?: string;
    passing_score: number;
    max_attempts: number;
    time_limit?: number; // in minutes
    is_active: boolean;
}

// =============================================================================
// VIEW MODEL SHAPES (interface)
// =============================================================================

/**
 * Student Dashboard Shape - Multi-field view model
 * Used for dashboard API responses and component props
 */
export interface StudentDashboardShape {
    student: StudentType;
    course_auths: CourseAuthType[];
    active_courses: CourseAuthType[];
    completed_courses: CourseAuthType[];
    total_progress: number;
    recent_activity: StudentActivityType[];
    upcoming_exams: ExamAuthType[];
    status: DashboardView;
}

/**
 * Class Dashboard Shape - Multi-field view model
 * Used for classroom/live session dashboard
 */
export interface ClassDashboardShape {
    course: CourseType;
    instructor: InstructorType;
    students: StudentType[];
    current_lesson?: CourseUnitLessonType;
    session_status: 'active' | 'scheduled' | 'completed';
    start_time?: DateTimeString;
    end_time?: DateTimeString;
}

/**
 * Student Activity Domain Model
 * For tracking and displaying student learning activities
 */
export interface StudentActivityType {
    id: number;
    type: ActivityType;
    title: string;
    description: string;
    timestamp: DateTimeString;
    course_id?: CourseId;
    course_title?: string;
    progress?: number;
    metadata?: Record<string, any>;
}

// =============================================================================
// API RESPONSE TYPES
// =============================================================================

/**
 * Standard API Response Shape
 */
export interface ApiResponseShape<T = any> {
    success: boolean;
    message: string;
    data?: T;
    errors?: Record<string, string[]>;
    meta?: {
        total?: number;
        per_page?: number;
        current_page?: number;
        last_page?: number;
    };
}

/**
 * Course Progress Shape - Multi-field view model
 */
export interface CourseProgressShape {
    course_auth: CourseAuthType;
    progress_percentage: number;
    completed_units: number;
    total_units: number;
    completed_lessons: number;
    total_lessons: number;
    time_spent: number; // in minutes
}

// =============================================================================
// FORM & INPUT TYPES
// =============================================================================

/**
 * Student Registration Form Shape
 */
export interface StudentRegistrationShape {
    fname: string;
    lname: string;
    email: EmailAddress;
    password: string;
    password_confirmation: string;
    email_opt_in?: boolean;
    student_info?: Record<string, any>;
}

/**
 * Student Profile Update Form Shape
 */
export interface StudentProfileShape {
    fname: string;
    lname: string;
    email: EmailAddress;
    avatar?: string;
    use_gravatar?: boolean;
    email_opt_in?: boolean;
    student_info?: Record<string, any>;
}

// =============================================================================
// API RESPONSE TYPES (type)
// =============================================================================

export type StudentDashboardResponse = ApiResponseShape<StudentDashboardShape>;
export type CourseProgressResponse = ApiResponseShape<CourseProgressShape>;
