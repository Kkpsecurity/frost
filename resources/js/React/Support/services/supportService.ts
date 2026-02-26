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

// NOTE: These functions are dead code — endpoints do not exist.
// Real search uses /admin/support/search-users (StudentSearch.tsx via axios directly)
// Real poll uses /admin/support/poll-data (useSupportPoll.ts hook directly)

export const fetchSupportStats = async (): Promise<SupportStatsResponse> => {
    const { data } = await axios.get<SupportStatsResponse>(
        "/admin/support/poll-data"
    );
    return data;
};

export const searchSupportStudents = async (
    query: string
): Promise<SupportStudentSearchResponse> => {
    const { data } = await axios.get<SupportStudentSearchResponse>(
        "/admin/support/search-users",
        { params: { query } }
    );
    return data;
};
