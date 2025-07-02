import React, { useEffect, useState } from "react";
import apiClient from "../../Config/axios";
import { LaravelAdminShape, InstUnitLessonType } from "../../Config/types";



interface usePauseClassRoomHookProps {
    laravel: LaravelAdminShape;
    courseDateId: number;
    activeLesson: number | null;
    instUnitLesson: InstUnitLessonType;
}

const usePauseClassRoomHook = ({
    laravel,
    courseDateId,
    activeLesson,
    instUnitLesson,
}: usePauseClassRoomHookProps) => {
    /**
     * Pause the Lesson
     */
    const [isPaused, setIsPaused] = useState<boolean>(false);
    const [show, setShow] = useState<boolean>(false);

    /**
     * Pause the Lesson
     */
    const togglePauseLesson = (type: "true" | "false") => {
        apiClient.post(
            laravel.site.base_url + "/admin/instructors/pause-lesson/" + type,
            {
                courseDateId: courseDateId,
                lessonId: activeLesson,
            }
        );

        // Convert string "true" or "false" to boolean and update state
        setIsPaused(type === "true");
    };

    // Handle isPaused state
    useEffect(() => {
        if (instUnitLesson?.is_paused) {
            setIsPaused(true);
        } else {
            setIsPaused(false);
        }
    }, [instUnitLesson]);

    return {
        isPaused,
        togglePauseLesson,
        show,
        setShow,
    };
};

export default usePauseClassRoomHook;
