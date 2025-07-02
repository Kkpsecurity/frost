import React, { useState, useEffect, useRef } from "react";
import apiClient from "../../../../../Config/axios";
import { CourseAuthType, StudentType, StudentUnitType } from "../../../../../Config/types";
import { useQuery } from "@tanstack/react-query";

interface StudentDataProps {
    courseDateId: number | null;
    setShow: React.Dispatch<React.SetStateAction<boolean>>;
    setValidateType: React.Dispatch<React.SetStateAction<string | null>>;
    setHeadshot: React.Dispatch<React.SetStateAction<string | string[] | null>>;
    setIdcard: React.Dispatch<React.SetStateAction<string | null>>;
}

export const useStudentData = ({
    courseDateId,
    setShow,
    setValidateType,
    setHeadshot,
    setIdcard,
}: StudentDataProps) => {
    const [selectStudentId, setSelectStudentId] = useState<number | null>(null);
    const [studentAuthId, setStudentAuthId] = useState<number | null>(null);
    const [courseAuths, setCourseAuths] = useState<CourseAuthType[] | []>([]);
    const [selectedStudent, setSelectedStudent] = useState<StudentType | null>(
        null
    );
    const [studentUnit, setStudentUnit] = useState<StudentUnitType | null>(null);

    const ValidateStudent = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        setShow(true);
        setValidateType("validate");
        setStudentAuthId(parseInt(e.currentTarget.id));
    };

    const ViewStudentCard = (studentId: number) => {
        setSelectStudentId(studentId);
    };

    const fetchStudentData = async () => {
        if (selectStudentId === null) {
            console.error("No student selected"); 
            return; // Exit early if no student ID is selected
        }
    
        const response = await apiClient.get(
            `admin/instructors/get_student_detail/${selectStudentId}/${courseDateId}`
        );
        
        setSelectedStudent(response.data.student); // Set the selected student data
        setStudentUnit(response.data.student.studentUnit); // Set the student unit data
        setCourseAuths(response.data.student.courseAuths); // Set the course auth data
        
        return response.data.student; // Directly return the student data
    };
    
    const {
        data: studentData,
        isError: isStudentError,
        error: studentError,
    } = useQuery(
        ["studentData", selectStudentId, courseDateId],
        fetchStudentData,{refetchInterval: 10000, enabled: !!selectStudentId,}
    );    

    useEffect(() => {
        // Format today's date for comparison
        const today = new Date();
        today.setHours(0, 0, 0, 0);  // Set time to midnight for accurate day comparison
        
        // Check if a student is selected
        if (selectedStudent && selectedStudent.validations) {
            const headshots = selectedStudent.validations.headshot;
            if (headshots && typeof headshots === 'object') {
                // Convert timestamp keys to dates and find today's headshot
                let todaysHeadshot = '';
                Object.keys(headshots).forEach(key => {
                    const dateOfHeadshot = new Date(parseInt(key));
                    dateOfHeadshot.setHours(0, 0, 0, 0);
                    if (dateOfHeadshot.getTime() === today.getTime()) {
                        todaysHeadshot = headshots[key];
                    }
                });
                setHeadshot(todaysHeadshot || "");  // Set today's headshot or empty string if none found
            } else {
                setHeadshot(selectedStudent.validations.headshot);  // Set headshot from validations
            }
    
            // Set ID card image from validations
            setIdcard(selectedStudent.validations.idcard ?? "");
        } else if (selectStudentId !== null) {
            // If no corresponding student data,
            // reset the selected student and clear headshot and idcard
            setSelectedStudent(null);
            setHeadshot("");
            setIdcard("");
        }
    
        // Handle student errors
        if (studentError) {
            console.error("Error fetching student data:", studentError);
            setSelectedStudent(null);
            setHeadshot("");
            setIdcard("");
        }
    }, [selectedStudent, selectStudentId, studentError]);

    return {
        ValidateStudent,
        ViewStudentCard,
        setStudentAuthId,
        studentAuthId,
        courseAuths,
        selectedStudent,
        studentUnit,
        setSelectedStudent,
        selectStudentId,
        setSelectStudentId,
    };
};
