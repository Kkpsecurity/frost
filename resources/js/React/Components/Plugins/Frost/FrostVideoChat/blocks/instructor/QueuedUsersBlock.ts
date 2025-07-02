import React, { useEffect, useState } from "react";
import { UserListBlockType } from "../../../../../../Config/types";
import { getQueuedUsers } from "../../../../../../Hooks/Admin/useVideoCallQueuedUsers";

const QueuedUsersBlock = ( courseDateId ) => {
    // console.log("courseDateId", courseDateId);  
    
    /**
     * List of all students in the queue
     */
    const [allQueueStudents, setAllQueueStudents] = useState<
        UserListBlockType[]
    >([]);

    /**
     * Get the list of students in the queue from the API
     */
    const { queuedUsers, error, status } = getQueuedUsers(courseDateId);

    /**
     * List of all students in the queue
     */
    useEffect(() => {
        if (queuedUsers && queuedUsers.success === true) {
            const qUsers: UserListBlockType[] = Object.values(
                queuedUsers.queues
            ).map((student) => ({
                id: student.id,
                course_auth_id: student.course_auth_id,
                fname: student.fname,
                lname: student.lname,
                email: student.email,
                avatar: student.avatar,
                created_at: student.created_at,
            }));
            
            setAllQueueStudents(qUsers);
        } else {
            setAllQueueStudents([]);
        }
    }, [queuedUsers]);

    return { allQueueStudents, status, error };
};

export default QueuedUsersBlock;
