export interface StudentType {
    id: number;
    name: string;
    email: string;
    avatar?: string;
}

export interface ChatUser {
    id: number;
    name: string;
    email: string;
    user_type: string;
    avatar?: string;
}

export interface ReturnChatMessageType {
    id: number;
    user_id: number;
    message: string;
    user_type: string;
    created_at: string;
    updated_at: string;
    user: {
        id: number;
        name: string;
        email: string;
        avatar?: string;
    };
}

export interface FrostMessage {
    user_id: number;
    message: string;
    course_date_id: string;
    user_type: string;
}
