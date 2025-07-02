import { useEffect, useState } from "react";

import apiClient from "../../Config/axios";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";

import { ValidatedInstructorShape, StudentType } from "../../Config/types";
import { type } from "os";

let debug = false;

/**
 * Updates the zoom data for a courses
 * @param data
 * @returns
 */
const updateZoomData = (data) => {
    return apiClient
        .post("/admin/instructors/update_zoom_data", data)
        .then((response) => {
            if (debug === true) console.log("ZoomData: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("ZoomData Error:", error);
            return error;
        });
};

export const useBeginANewDay = () => {
    const queryClient = useQueryClient();

    const mutation = useMutation(
        (courseDateId) =>
            apiClient.post(`/admin/instuctors/assign/${courseDateId}`, {
                course_date_id: courseDateId,
            }),
        {
            onSuccess: () => {
                // Invalidate and refetch something when the mutation is successful
                queryClient.invalidateQueries(["course"]);
            },
        }
    );

    return {
        mutate: mutation.mutate, // Use this to trigger the mutation
        mutateAsync: mutation.mutateAsync, // Use this if you want to handle promises
        isLoading: mutation.isLoading, // Will be true while the mutation is in progress
        isError: mutation.isError, // Will be true if an error occurred during the mutation
        error: mutation.error, // The error object if there was an error
        isSuccess: mutation.isSuccess, // Will be true if the mutation was successful
    };
};

/**
 * Assigns an assistant to a course
 */
export const useAssitantTakeOver = () => {
    const queryClient = useQueryClient();

    const mutation = useMutation(
        ({ courseDateId }: { courseDateId: number }) =>
            apiClient.post(`/admin/instructors/reassign`, {
                courseDateId,
            }),
        {
            onSuccess: () => {
                // Invalidate and refetch something when the mutation is successful
                queryClient.invalidateQueries(["course"]);
            },
        }
    );

    return {
        mutate: mutation.mutate, // Use this to trigger the mutation
        mutateAsync: mutation.mutateAsync, // Use this if you want to handle promises
        isLoading: mutation.isLoading, // Will be true while the mutation is in progress
        isError: mutation.isError, // Will be true if an error occurred during the mutation
        error: mutation.error, // The error object if there was an error
        isSuccess: mutation.isSuccess, // Will be true if the mutation was successful
    };

    // const queryClient = useQueryClient();

    // const mutation = useMutation(
    //     () =>
    //         apiClient.post(`/admin/instructors/reassign`, {
    //             courseDateId: courseDateId,
    //         }),
    //     {
    //         onSuccess: () => {
    //             // Invalidate and refetch something when the mutation is successful
    //             queryClient.invalidateQueries(["course"]);
    //         },
    //     }
    // );

    // return {
    //     mutate: mutation.mutate, // Use this to trigger the mutation
    //     mutateAsync: mutation.mutateAsync, // Use this if you want to handle promises
    //     isLoading: mutation.isLoading, // Will be true while the mutation is in progress
    //     isError: mutation.isError, // Will be true if an error occurred during the mutation
    //     error: mutation.error, // The error object if there was an error
    //     isSuccess: mutation.isSuccess, // Will be true if the mutation was successful
    // };
};

export const useAssitantModerate = () => {
    const queryClient = useQueryClient();

    const mutation = useMutation(
        ({
            courseDateId,
            type,
        }: {
            courseDateId: number;
            type: string | null;
        }) =>
            apiClient.post(`/admin/instructors/assign/assistant`, {
                courseDateId,
                type,
            }),
        {
            onSuccess: () => {
                // Invalidate and refetch something when the mutation is successful
                queryClient.invalidateQueries(["course"]);
            },
        }
    );

    return {
        mutate: mutation.mutate, // Use this to trigger the mutation
        mutateAsync: mutation.mutateAsync, // Use this if you want to handle promises
        isLoading: mutation.isLoading, // Will be true while the mutation is in progress
        isError: mutation.isError, // Will be true if an error occurred during the mutation
        error: mutation.error, // The error object if there was an error
        isSuccess: mutation.isSuccess, // Will be true if the mutation was successful
    };
};

/**
 * Marks a lesson as complete
 * @param data
 * @returns
 */
const makeLessonComplete = (data) => {
    return apiClient
        .post("/admin/instructors/complete_lesson", data)
        .then((response) => {
            if (debug === true) console.log("Lesson Completed: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("Lesson Completed Error:", error);
            return error;
        });
};

/**
 * Marks a course as complete
 * @param data
 * @returns
 */
const makeCourseComplete = (course_date_id) => {
    return apiClient
        .post("/admin/instructors/complete_course", {
            course_date_id: course_date_id,
        })
        .then((response) => {
            console.log(response);
        })
        .catch((error) => {
            console.log(error);
        });
};

/**
 * Invalidate the course query
 * @returns
 */
export const useCompleteLesson = () => {
    const queryClient = useQueryClient();
    return useMutation(makeLessonComplete, {
        onSuccess: () => {
            queryClient.invalidateQueries(["course"]);
        },
    });
};

/**
 * Invalidate the course query
 * @returns
 */
export const useCompleteCourse = () => {
    const queryClient = useQueryClient();
    return useMutation(makeCourseComplete, {
        onSuccess: () => {
            queryClient.invalidateQueries(["course"]);
        },
    });
};

/**
 * Get the validated instructor data
 * @returns
 */
export const useValidatedInstructorHook = () => {
    const { data, status, error } = useQuery<ValidatedInstructorShape>(
        ["instructor"],
        async () => {
            const response = await apiClient.get("/admin/instructors/validate");

            console.log("useValidatedInstructorHook: ", response);
            return response.data;
        },
        {
            cacheTime: 6000 * 30,
            staleTime: 30000,
            refetchOnMount: true,
            refetchOnWindowFocus: "always",
            refetchInterval: 60000,
        }
    );

    return { data, status, error };
};

/**
 * Get the active course data
 * @returns
 */
export const useActiveCourseData = () => {
    const { data, status } = useQuery(
        ["course"],
        async () => {
            const response = await apiClient.get(
                `/admin/instructors/portal/course/get`
            );
            console.log("useActiveCourseData: ", response);
            return response.data;
        },
        {
            cacheTime: 6000 * 30,
            staleTime: 20000,
            refetchOnMount: true,
            refetchOnWindowFocus: "always",
            refetchInterval: 15000,
        }
    );
    console.log("useActiveCourseDataDatData: ", data);

    const error = status === "error" ? Promise.reject(data) : undefined;

    return { data, status, error };
};

export const useCompleteCourseData = (course_date_id: number) => {
    return apiClient
        .get("/admin/instructors/completed/" + course_date_id + "/")
        .then((response) => {
            console.log("Course Completed: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("Course Completed Error:", error);
            return error;
        });
};

/**
 * Get the students data
 * @param course_date_id
 * @returns
 */
export const useActiveStudentHook = (
    course_date_id: number | null,
    page: number,
    search: string = ""
) => {
    const { data, status } = useQuery(
        ["students", course_date_id, page, search],
        async () => {
            const response = await apiClient.get(
                `/admin/instructors/get_students/${course_date_id}/${page}/${search}`
            );

            return response.data;
        },
        {
            cacheTime: 6000 * 30,
            staleTime: 20,
            refetchOnMount: true,
            refetchOnWindowFocus: "always",
            refetchInterval: 10000,
        }
    );

    const isLoading = status === "loading";
    const error = status === "error" ? Promise.reject(data) : undefined;

    return { data, isLoading, error };
};

/**
 * Updates Student Data
 * @param data
 * @returns
 */
const updateStudent = (data) => {
    return apiClient
        .post("/admin/instructors/validate/student", data)
        .then((response) => {
            if (debug === true) console.log("Validate Student: ", response);
            return response.data;
        })
        .catch((error) => {
            const errorMessage =
                error.response &&
                error.response.data &&
                error.response.data.message
                    ? error.response.data.message
                    : error.message;
            console.error("ZoomData Error:", errorMessage);
            throw new Error(errorMessage);
        });
};

export const validateStudentHook = () => {
    const queryClient = useQueryClient();

    const mutation = useMutation(updateStudent, {
        onSuccess: () => {
            queryClient.invalidateQueries(["students"]);
        },
        onError: (error) => {
            console.error("Error updating student:", error);
            // You can further process the error here if needed
        },
    });

    return {
        ...mutation,
        error: mutation.error,
    };
};

export const updateMeetingData = () => {
    const queryClient = useQueryClient();
    const mutation = useMutation(updateZoomData, {
        onSuccess: () => {
            queryClient.invalidateQueries(["course"]);
        },
    });

    return {
        mutateAsync: mutation.mutateAsync,
        isLoading: mutation.isLoading,
        isError: mutation.isError,
        error: mutation.error,
        isSuccess: mutation.isSuccess,
    };
};

const updateLesson = (data) => {
    return apiClient
        .post("/admin/instructors/active_lesson", data)
        .then((response) => {
            if (debug === true) console.log("updateLesson: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("updateLesson Error:", error);
            return error;
        });
};

export const useUpdateLesson = () => {
    const queryClient = useQueryClient();
    return useMutation(updateLesson, {
        onSuccess: () => {
            queryClient.invalidateQueries(["course"]);
        },
    });
};

/**
 * Delete a photo
 * @param data
 * @returns
 */
const deletePhoto = (data) => {
    return apiClient
        .post("/admin/instructors/student/delete-file", data)
        .then((response) => {
            if (debug === true) console.log("deletePhoto: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("deletePhoto Error:", error);
            return error;
        });
};

export const useDeletePhoto = () => {
    const queryClient = useQueryClient();
    return useMutation(deletePhoto, {
        onSuccess: () => {
            queryClient.invalidateQueries(["course"]);
        },
    });
};

export const getPreviousCourses = (instructor_id) => {
    return apiClient
        .get("/admin/instructors/previous_courses/" + instructor_id + "/")
        .then((response) => {
            if (debug === true) console.log("getPreviousCourses: ", response);
            return response.data;
        })
        .catch((error) => {
            console.error("getPreviousCourses Error:", error);
            return error;
        });
};
