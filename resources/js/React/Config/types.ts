import { initial } from "lodash";

export interface BaseLaravelShape {
    success: boolean;
    message: string;
    settings: LaravelSettingsType;
    config: AppConfigType;
    site: SiteConfigType;
}

export interface LaravelDataShape extends BaseLaravelShape {
    error: any;
    user: StudentType;
}

export interface StudentTabType {
    success: boolean | null;
    message: string | null;
    unverified: {
        students: StudentType[];
        current_page: number;
        last_page: number;
        total: number;
    };
    verified: {
        students: StudentType[];
        current_page: number;
        last_page: number;
        total: number;
    };
    inactive: {
        students: StudentType[];
        current_page: number;
        last_page: number;
        total: number;
    };
}

export interface LaravelAdminShape extends BaseLaravelShape {
    user: InstructorType;
}

export interface MessageConsoleType {
    status: "success" | "danger" | "warning" | "info" | "default";
    message: string;
}

export interface SuppportClassDataType {
    sucess: boolean;
    message: string;
    courses: CourseType[];
    lessons: CourseLessonType[];
    courseUnits: CourseUnitType[];
    selectCourseAuthId: number;
    studentActivity: CourseActivityType[];
    studentUnits: StudentUnitType[];
}

export interface ClassDataShape {
    [x: string]: any;
    success: boolean;
    message: string;
    student_unit_id: number | null;
    start_date: string;
    end_date: string;
    is_live_class: boolean;
    inst_unit_zoom_started: boolean;
    course: CourseType;
    courseDate: CourseDateType;
    courseAuth: CourseAuthType | null;
    courseLessons: CourseLessonType[];
    courseUnitLessons: CourseUnitLessonType[] | null;
    allLessonsTotal: number;
    allCompletedStudentLessonsTotal: number;
    instUnit: InstUnitType | null;
    instructor: InstructorType | null;
    instUnitLesson: InstUnitLessonType;
    studentUnit: StudentUnitType;
    studentLesson: StudentLessonType;
    completedStudentLessons: StudentLessonType[];
    isChatEnabled: boolean;
    validations: {
        headshot: string;
        idcard: string;
        authAgreement: boolean;
        message: string;
    } | null;
    completedInstLessons:
        | {
              lesson_id: number;
              completed_at: string;
          }[]
        | null;
    zoomData: {
        meeting_id: string;
        meeting_passcode: string;
    };
    challenge: ChallengeType;
    studentExam: StudentExamCheck;
    lessonInProgress: boolean;
    documents: [];
    lessonPaused: boolean;
    gettingStarted: boolean;
    assistant: InstructorType | null;
    appVersion: number;
}

export interface CourseAuthType {
    id: number;
    user_id: number;
    course_id: number;
    created_at: string;
    updated_at: string;
    agreed_at: string | null;
    completed_at: string | null;
    is_passed: boolean;
    start_date: string;
    expire_date: string;
    disabled_at: string | null;
    disabled_reason: string | null;
    submitted_at: string | null;
    submitted_by: string | null;
    dol_tracking: string | null;
    exam_admin_id: number | null;
    range_date_id: number | null;
}

export interface CourseActivityType {
    user_id: number;
    created_at: string;
    started_at: string;
    agreed_at: string;
    completed_at: string;
    expires_at: string;
    disabled_at: string;
    browser: string;
}

export interface CourseUnitType {
    [x: string]: any;
    id: number;
    course_id: number;
    title: string;
    admin_title: string;
    ordering: number;
    created_at: string;
}

export interface CourseUnitDayType {
    CourseUnit: CourseUnitType;
    lessons: CourseLessonType[];
    studentUnit: StudentUnitType;
    totalStudentUnitCount: number;
    studentUnitCompleted: number;
}

export interface SupportClassDataShape {
    success: boolean;
    message: string;
    student: StudentType;
    StudentUnits: StudentUnitType[];
    courseUnitId: number;
    courseDateId: number;
    instUnitId: number;
    courses: CourseType[];
    CourseAuths: CourseAuthType[];
    studentActivities: CourseActivityType[];
    CourseUnits: CourseUnitType[];

    // Assuming CourseUnitDayType and InstCourseUnit are previously defined types
    // The following properties represent data for each course unit and its corresponding instructor unit.
    // Adjust the names and types based on the actual data structure you have.

    CourseUnit: CourseUnitDataType; // Adjusted to CourseUnitDataType which should represent the data structure for a course unit.
    InstUnitCourseUnit: InstCourseUnitType[]; // Adjusted to InstCourseUnitType which should represent the data structure for an instructor course unit.
}

export interface CourseUnitDataType {
    CourseUnit: CourseUnitType;
    lessons: CourseUnitLessonType[];
}

export interface InstCourseUnitType {
    InstUnit: InstUnitType | null;
    instUnitLesson: InstUnitLessonType;
    InstUnitActivity: {
        classIsLive: boolean;
        completedInstLessons: CourseUnitLessonType[];
        activeLesson: CourseUnitLessonType | null;
    };
}

export interface StudentExamCheck {
    is_ready: boolean | null;
    next_attempt_at: string | null;
}

export interface StudentExamType {
    id: number;
    course_auth_id: number;
    uuid: string;
    created_at: string;
    expires_at: string;
    next_attempt_at: string;
    completed_at: string | null;
    hidden_at: string | null;
    hidden_by: string | null;
    score: number | null;
    is_passed: boolean;
    question_ids: number[];
    answers: string | null;
    incorrect: string | null;
}

export interface ChallengeType {
    id: number;
    isChallengeReady: boolean;
    isChallengeEOLReady: boolean;
    challenge_time: string;
    challenge_id: number;
    is_final: boolean;
    created_at: number;
    completed_at: number | null;
}

export interface ChallengeListType {
    id: number;
    student_lesson_id: number;
    is_final: boolean;
    is_eol: boolean;
    created_at: string;
    updated_at: string;
    expires_at: string;
    completed_at: string | null;
    failed_at: string | null;
}

export interface ValidatedInstructorShape {
    success: boolean;
    message: string;
    instructor: InstructorType;
    courseDateId: number;
    courses: CourseType[];
}

export interface CallRequestType {
    created_at: string;
    user_id: number;
    fname: string;
    lname: string;
    email: string;
}

export interface CourseMeetingShape {
    success: boolean;
    message: string;
    courseDate: CourseDateType;
    instructor: InstructorType;
    course: CourseType;
    instUnit: InstUnitType;
    studentUnits: StudentUnitType[];
    lessons: CourseLessonType[];
    instUnitLesson: InstUnitLessonType;
    instructorCanClose: boolean;
    completedLessons: completedLessonType[];
    courseUnitLessons: CourseUnitLessonType[];
    isChatEnabled: boolean;
    callRequest: CallRequestType[];
    assistant: InstructorType | null;
    appVersion: string;
    totalStudentsCount: number; // New field for the total student count
    completedStudentsCount: number; // New field for the completed student count
}

export interface completedLessonType {
    lesson_id: number;
    completed_at: string;
}

export interface LaravelSettingsType {
    company_name: string;
    company_address: string;
    company_city: string;
    company_state: string;
    company_zip: string;
    company_phone: string;
    company_email: string;
    company_website: string;
    company_logo: string;
}

export interface AIAgentsConfigType {
    openai: {
        enable_ai: boolean;
        api_key: string;
        org_id: string;
        url: string;
        default_model: string;
        default_system_role: string;
        default_temperature: number;
    };
    write_progress: {
        file_path: string;
        default_message: string;
    };
}

export interface AppConfigType {
    zoom: ZoomConfigType;
    agora: AgoraConfigType;
    aiagents: AIAgentsConfigType;
    site: SiteConfigType;
}

export interface SiteConfigType {
    base_url: string;
}

export interface AgoraConfigType {
    app_id: string;
    certificate: string;
    rtc: {
        endpoint: string;
        expire_minutes: number;
    };
}

export interface AgoraVideoConfigType {
    mode: string;
    codec: string;
    appId: string;
    channelName: string;
    token: string;
}

export interface ZoomConfigType {
    api_key: string; // JWT Deprecated
    api_secret: string; // JWT Deprecated
    meeting_sdk: string;
    meeting_secret: string;
    api_url: string;
    token_life: number;
    authentication_method: string;
    max_api_calls_per_request: number;
    signature_endpoint: string;
    screen_share_url: string;
}

export interface ZoomPayloadType {
    zoom_email: string;
    use_pmi: boolean;
    pmi: string;
    zoom_status: string;
    zoom_password: string | null;
    zoom_passcode: string | null;
    encrypted_passcode: string | null;
}

export interface InstructorType {
    id: number;
    is_active: boolean;
    role_id: number;
    lname: string;
    fname: string;
    email: string;
    created_at: string;
    updated_at: string;
    avatar: string | null;
    use_gravatar: boolean;
    userRole: string; // added not part of the db
    zoom_last_validated: string | null;
    zoom_payload: ZoomPayloadType;
}

export interface InstUnitType {
    id: number;
    course_date_id: number;
    created_at: string | null;
    created_by: number | null;
    completed_at: string | null;
    completed_by: string | null;
    assistant_id: number | null;
}

export interface InstUnitLessonType {
    id: number;
    inst_unit_id: number;
    lesson_id: number;
    is_paused: boolean;
    created_at: string;
    created_by: number;
    completed_at: string;
    completed_by: number;
}

export interface CourseDateType {
    id: number;
    is_active: boolean;
    course_unit_id: number;
    starts_at: string;
    ends_at: string;
}

export interface CourseType {
    length: number;
    id: number;
    title: string;
    title_long: string;
    created_at: string;
    starts_at: string;
    ends_at: string;
    InstUnit: InstUnitType;
    createdBy?: string;
    assistantBy?: string;
}

export interface CourseLessonType {
    id: number;
    title: string;
    credit_minutes: number;
    course_unit_id: number;
    lesson_id: number;
    created_at: string;
    completed_at: string | null;
}

export interface ChatUser {
    user_id: number;
    user_name: string;
    user_avatar: string;
    user_type: "instructor" | "student";
}

export interface FrostChatCardProps {
    chatUser: ChatUser;
    darkMode: boolean;
    debug: boolean;
}

export interface FrostMessage {
    id: number;
    user_id: number;
    user_type: "student" | "instructor";
    body: string;
}

export interface ReturnChatMessageType {
    id: string;
    user: ChatUser;
    body: string;
    created_at: string;
    updated_at: string;
}

export interface CourseUnitLessonType {
    id: number;
    course_unit_id: number;
    lesson_id: number;
    progress_minutes: number;
    instr_seconds: number;
    ordering: number;
}

export interface ProfileDataShape {
    then(arg0: (data: any) => void): unknown;
    success: boolean;
    message: string;
    user: ProfileType;
    paymentHistory: {
        id: number;
        user_id: number;
        amount: number;
        payment_method: string;
        status: string;
        created_at: string;
        updated_at: string;
    }[];
}

export interface ProfileType {
    id: number;
    is_active: boolean;
    role_id: number;
    lname: string;
    fname: string;
    email: string;
    created_at: string;
    updated_at: string;
    avatar: string | null;
    use_gravatar: boolean;
    zoom_last_validated: string | null;
    zoom_payload: ZoomPayloadType;
}

export interface StudentType {
    length: number;
    id: number;
    is_active: boolean;
    role_id: number;
    lname: string;
    fname: string;
    email: string;
    created_at: string;
    updated_at: string;
    avatar?: string | null;
    use_gravatar?: boolean;
    course_id: number;
    course_auth_id: number;
    course_date_id: number | null;
    course_unit_id: number | null;
    student_unit_id: number | null;

    studentUnit: StudentUnitType | null;
    studentLessons: StudentLessonType[];
    courseAuth: CourseAuthType;
    courseAuths: CourseAuthType[];
    currentStudentUnit: StudentUnitType | null;

    student_info: {
        map(
            arg0: (
                info: any,
                index: any
            ) => import("react/jsx-runtime").JSX.Element
        ): unknown;
        fname: string;
        initial: string;
        lname: string;
        email: string;
        dob: string;
        suffix: string;
        phone: string;
    };
    validations: {
        headshot: string | string[] | null;
        idcard: string | null;
        headshot_status: number | null;
        idcard_status: number | null;
        authAgreement: boolean;
        message: string;
    };
}

export interface StudentUnitType {
    id: number;
    course_auth_id: number;
    course_date_id: number;
    course_unit_id: number;
    inst_unit_id: number;
    created_at: number;
    completed_at: number;
    verified: {
        type: string;
        instructor: string;
        course_id: number;
        license_type: string;
        timestamp: string;
    } | null;
    validation_action: string;
    currentStudentLesson: StudentLessonType | null;
    studentLessons: StudentLessonType[];
    ejected_at: string | null;
    ejected_for: string | null;
}

export interface StudentLessonType {
    id: number;
    student_unit_id: number;
    lesson_id: number;
    inst_lesson_id: number;
    created_at: string;
    updated_at: string;
    dnc_at: string | null;
    completed_at: string | null;
    completed_by: number | null;
    unit_completed: boolean;
}

export interface StudentRequirementsType {
    classRulesAgreement: {
        agreedToRules: string; // Date as string or 'null' if not agreed
    };
    identityVerification: {
        headshot: string | null; // URL as a string or 'null'
        idcard: string | null; // URL as a string or 'null'
    };
    studentAgreement: {
        agreed: boolean; // true if agreed to the terms of service
    };
}

export interface DetermineValidationRequirementType {
    laravel: LaravelDataShape;
    studentRequirements: StudentRequirementsType;
    setStudentRequirements: React.Dispatch<StudentRequirementsType>;
    debug?: boolean;
}

export interface UserListBlockType {
    id: number;
    course_auth_id: number;
    fname: string;
    lname: string;
    email: string;
    avatar: string;
    created_at: string;
}

export interface QueuedUserReturnType {
    id: number;
    success: boolean;
    message: string;
    queues: StudentType[];
}

export interface listenCallReturnType {
    caller_id: number;
    success: boolean;
    message: string;
}

export interface StudentMessageType {
    message?: string;
    detail?: string;
    bgColor?: string;
}
