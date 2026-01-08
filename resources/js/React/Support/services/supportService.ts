import axios from "axios";

export type SupportStatsResponse = {
    success: boolean;
    data?: unknown;
    message?: string;
};

export type SupportStudentSearchResponse = {
    success: boolean;
    data?: unknown;
    count?: number;
    message?: string;
};

export const fetchSupportStats = async (): Promise<SupportStatsResponse> => {
    const { data } = await axios.get<SupportStatsResponse>(
        "/admin/frost-support/stats"
    );
    return data;
};

export const searchSupportStudents = async (
    query: string
): Promise<SupportStudentSearchResponse> => {
    const { data } = await axios.get<SupportStudentSearchResponse>(
        "/admin/frost-support/search-students",
        { params: { query } }
    );
    return data;
};
