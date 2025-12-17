/**
 * Dashboard Types
 *
 * Following React Types & Interfaces Rules:
 * - type = single-dimension props or atomic aliases
 * - interface = multi-field shapes (domain models)
 * - Type suffix for domain models, Shape suffix for view models
 */

// =============================================================================
// ATOMIC ALIASES (type)
// =============================================================================

export type DashboardId = number;
export type SessionId = number;
export type ProgressPercentage = number;

// =============================================================================
// DOMAIN MODELS (interface with Type suffix)
// =============================================================================

/**
 * AuthUserType Interface
 * References: App\Models\User
 * Table: users
 * Represents authenticated user data used throughout dashboard components
 */
export interface AuthUserType {
    id: number;
    fname: string;
    lname: string;
    email: string;
    is_active: boolean;
    role_id: number;
    avatar?: string;
    use_gravatar: boolean;
    student_info?: Record<string, any>;
    email_opt_in: boolean;
    created_at: string;
    updated_at: string;
    email_verified_at?: string;

    // Virtual/computed properties from Laravel model
    name?: string; // From getNameAttribute()
    fullname?: string; // From fullname() method

    // Relationships (when loaded)
    role?: {
        id: number;
        name: string;
    };
    course_auths?: CourseAuthType[];
    user_prefs?: Array<{
        id: number;
        user_id: number;
        key: string;
        value: any;
    }>;
    user_browser?: {
        id: number;
        user_id: number;
        browser_info: Record<string, any>;
    };
}

/**
 * CourseAuthType Interface
 * References: App\Models\CourseAuth
 * Table: course_auths
 * Represents course authorization/purchase data with full Laravel model structure
 */
export interface CourseAuthType {
    id: number;
    user_id: number;
    course_id: number;
    created_at: string;
    updated_at: string;
    agreed_at?: string;
    completed_at?: string;
    is_passed: boolean;
    start_date?: string;
    expire_date?: string;
    disabled_at?: string;
    disabled_reason?: string;
    submitted_at?: string;
    submitted_by?: number;
    dol_tracking?: string;
    exam_admin_id?: number;
    range_date_id?: number;
    id_override: boolean;

    // Relationships (when loaded)
    course?: {
        id: number;
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
    };
    user?: AuthUserType;
    student_units?: Array<{
        id: number;
        course_auth_id: number;
        course_unit_id: number;
        started_at?: string;
        completed_at?: string;
        is_completed: boolean;
    }>;
    exam_auths?: Array<{
        id: number;
        course_auth_id: number;
        exam_id: number;
        started_at?: string;
        completed_at?: string;
        score?: number;
        is_passed: boolean;
        attempts: number;
        max_attempts: number;
    }>;

    // Virtual/computed properties from Laravel model methods
    progress?: number;
    status?: "active" | "completed" | "expired" | "disabled";
    is_active?: boolean; // From IsActive() method
    is_expired?: boolean; // From IsExpired() method
    is_failed?: boolean; // From IsFailed() method
}

// =============================================================================
// VIEW MODEL SHAPES (interface with Shape suffix)
// =============================================================================

export interface StudentDashboardShape {
    student: AuthUserType;
    course_auths: CourseAuthType[];
    active_courses: CourseAuthType[];
    completed_courses: CourseAuthType[];
    total_progress: number;
    status: "ONLINE" | "OFFLINE";
}

export interface ClassDashboardShape {
    instructor: AuthUserType;
    course_dates: Array<{
        id: number;
        course_id: number;
        start_date: string;
        end_date: string;
        is_active: boolean;
    }>;
    current_session?: {
        id: number;
        title: string;
        start_time: string;
        end_time?: string;
        status: "active" | "scheduled" | "completed";
    };
}

// =============================================================================
// API RESPONSE SHAPES (interface with Shape suffix)
// =============================================================================

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

export interface StudentDashboardResponseShape
    extends ApiResponseShape<StudentDashboardShape> {}
export interface ClassDashboardResponseShape
    extends ApiResponseShape<ClassDashboardShape> {}

// =============================================================================
// UTILITY TYPES (type)
// =============================================================================

export type UserRole =
    | "admin"
    | "instructor"
    | "coordinator"
    | "assistant"
    | "student";
export type DashboardView = "ONLINE" | "OFFLINE";
export type CourseStatus =
    | "not_started"
    | "in_progress"
    | "completed"
    | "expired"
    | "failed";
