import { useMemo } from "react";

export type ClassroomState = 'offline' | 'pending' | 'online';

export interface ClassRouterResult {
    state: ClassroomState;
    isClassroomActive: boolean;
    isClassroomPending: boolean;
}

/**
 * Hook: useClassRouter
 *
 * Determines the current classroom state based on courseDates array and instUnit presence
 *
 * States:
 * - OFFLINE: No courseDates AND no instUnit (default, no class scheduled)
 * - PENDING: courseDates exist BUT no instUnit (classes scheduled, awaiting instructor start)
 * - ONLINE: courseDates exist AND instUnit exists (class started, instructor active)
 *
 * @param courseDates - Array of course dates from classroomData?.courseDates
 * @param instUnit - From instructorData?.instUnit
 * @returns ClassRouterResult with state and flags
 */
export const useClassRouter = (
    courseDates: any[],
    instUnit: any
): ClassRouterResult => {

    console.log("🎓 useClassRouter: Inputs", { courseDates, courseDatesCount: courseDates?.length, instUnit });

    return useMemo(() => {
        // Determine state
        let state: ClassroomState = 'offline';
        const hasCourseDates = courseDates && courseDates.length > 0;

        if (hasCourseDates && instUnit) {
            // Both exist: class is ONLINE
            state = 'online';
        } else if (hasCourseDates && !instUnit) {
            // CourseDates exist but no InstUnit: class is PENDING (awaiting start)
            state = 'pending';
        } else {
            // No courseDates: class is OFFLINE
            state = 'offline';
        }

        // Calculate flags
        const isClassroomActive = state === 'online';
        const isClassroomPending = state === 'pending';

        console.log("🎓 useClassRouter:", {
            state,
            hasCourseDate: hasCourseDates,
            hasInstUnit: !!instUnit,
            isClassroomActive,
            isClassroomPending,
        });

        return {
            state,
            isClassroomActive,
            isClassroomPending,
        };
    }, [courseDates, instUnit]);
};

export default useClassRouter;
