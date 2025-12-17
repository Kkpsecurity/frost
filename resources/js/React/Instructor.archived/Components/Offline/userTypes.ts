export interface UserData {
    id: number;
    fname: string;
    lname: string;
    name: string;
    email: string;
    role_id: number | null;
    role_name: string | null;
    is_sys_admin: boolean;
}

export interface SessionValidationResponse {
    authenticated: boolean;
    instructor?: UserData;
    course_date?: any;
    status?: string;
    message?: string;
}
