import React, { useEffect, useState } from "react";
import { UserListBlockType } from "../../../../../../Config/types";

const StudentUsersBlock = (allStudents, courseDateId) => {
    // console.log("courseDateId", courseDateId);

    /**
     * List of students in the class in the Student tab
     */
    const [studentsInClass, setStudentsInClass] =
        useState<UserListBlockType[]>(null);

    /**
     * List of students in the class
     * allStudents.array.objects
     */
    useEffect(() => {
        const sInClass: UserListBlockType[] = allStudents.map((student) => ({
            id: student.id,
            course_auth_id: student.course_auth_id,
            fname: student.fname,
            lname: student.lname,
            email: student.email,
            avatar: student.avatar,
            created_at: student.created_at,
        }));
        
        setStudentsInClass(sInClass);
    }, [allStudents]);

    return { studentsInClass, setStudentsInClass };
};

export default StudentUsersBlock;
