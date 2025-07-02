import React, { useCallback, useState } from "react";
import {
  useCompleteCourse,
    useCompleteLesson,
    useUpdateLesson,
} from "../../../../../Hooks/Admin/useInstructorHooks";
import apiClient from "../../../../../Config/axios";

export const useTopNavData = (
    course_date_id: number,
    activeLesson: number | null,
    setActiveLesson: React.Dispatch<React.SetStateAction<number | null>>
) => {
    /**
     * Lesson Complete Mutation
     */
    const { mutate: completeLesson } = useCompleteLesson();

    const { mutate: completeCourse } = useCompleteCourse();

    /**
     * Mark Lesson Complete
     */
    const MarkLessonComplete = useCallback(
        (event: React.MouseEvent<HTMLAnchorElement>) => {
            event.preventDefault();
            const lesson_id: number = parseInt(
                event.currentTarget.getAttribute("data-id")
            );
    
            // Check for user confirmation before proceeding
            if (!window.confirm("Are you sure you want to end this lesson?")) {
                return;  // User did not confirm, so we exit
            }
    
            // remove localstore for lesson start time
            localStorage.removeItem("lesson_start_time");
            setActiveLesson(null);
            completeLesson({
                course_date_id: course_date_id,
                lesson_id: lesson_id,
            });
        },
        [completeLesson, course_date_id]
    );

    /**
     * Mark Course/Day Completed 
     * @returns 
     */
    const MarkCourseComplete = () => {   
        
         // Check for user confirmation before proceeding
         if (!window.confirm("Are you sure you want to complete the day?")) {
            return;  // User did not confirm, so we exit
        }

        completeCourse({
            course_date_id: course_date_id,
        });
    };

    return { MarkLessonComplete, MarkCourseComplete };
};

// apiClient
// .post("/admin/instructors/complete_course", {
//     course_date_id: course_date_id,
// })
// .then((response) => {
//     console.log(response);
// })
// .catch((error) => {
//     console.log(error);
// });
