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
 * - ONLINE: instUnit exists (class started, instructor active)
 * - PENDING: courseDates exist BUT no instUnit (classes scheduled, awaiting instructor start)
 * - OFFLINE: No courseDates AND no instUnit (default, no class scheduled)
 *
 * @param courseDates - Array of course dates from classroomData?.courseDates
 * @param instUnit - From instructorData?.instUnit
 * @returns ClassRouterResult with state and flags
 */
export const useClassRouter = (
    courseDates: any[],
    instUnit: any
): ClassRouterResult => {

    console.log("🎓 useClassRouter: Inputs", {
        courseDates,
        courseDatesCount: courseDates?.length,
        instUnit,
        instUnitType: typeof instUnit,
        instUnitIsNull: instUnit === null,
        instUnitIsUndefined: instUnit === undefined,
        instUnitIsFalsy: !instUnit,
    });

    return useMemo(() => {
        // Determine state
        let state: ClassroomState = "offline";
        const hasCourseDates = courseDates && courseDates.length > 0;

        // ONLINE should be determined by instUnit, not courseDates.
        // courseDates come from a separate poll and can be transiently empty;
        // instUnit is the authoritative signal that class is live.
        if (instUnit) {
            state = "online";
        } else if (hasCourseDates) {
            // CourseDates exist but no InstUnit: class is PENDING (awaiting start)
            state = "pending";
        } else {
            // No courseDates: class is OFFLINE
            state = "offline";
        }

        // Calculate flags
        const isClassroomActive = state === "online";
        const isClassroomPending = state === "pending";

        console.log("🎓 useClassRouter:", {
            state,
            hasCourseDate: hasCourseDates,
            hasInstUnit: !!instUnit,
            instUnitValue: instUnit,
            instUnitId: instUnit?.id,
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
