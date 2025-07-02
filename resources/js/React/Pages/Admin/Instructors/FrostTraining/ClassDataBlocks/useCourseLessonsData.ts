import React, { useState } from "react";

import { useUpdateLesson } from "../../../../../Hooks/Admin/useInstructorHooks";

export const useCourseLessonsData = (courseDateId:  number) => {

  
    const [activeLesson, setActiveLesson] = useState<number | null>(null);

    /**
     * Student Validation Mutation
     */
    const { mutate: updateLesson } = useUpdateLesson();

    /**
     * ActivateLesson
     */
    const ActivateLesson = (event: React.MouseEvent<HTMLButtonElement>) => {
        event.preventDefault();
        const lesson_id: number = parseInt(
            event.currentTarget.getAttribute("data-id")
        );

        /**
         * Update Lesson
         */
        const activated = updateLesson({
            course_date_id: courseDateId,
            lesson_id: lesson_id,
        });

        setActiveLesson(lesson_id);

        // record time to local storage
        localStorage.setItem("lesson_start_time", new Date().toString());
    };

    return { ActivateLesson, setActiveLesson, activeLesson };
}
