import React, { useEffect } from "react";
import { Alert, Tab, Tabs } from "react-bootstrap";
import UnverifiedStudentList from "./UnverifedStudentList";
import VerifiedStudentList from "./VerifedStudentList";
import InactiveStudentList from "./InactiveStudentList";
import { InstUnitType, StudentTabType, StudentType } from "../../../../../Config/types";

interface StudentTabsProps {
    studentGroups: StudentTabType; 
    currentPage: number;
    setCurrentPage: React.Dispatch<React.SetStateAction<number>>;
    search: string;
    setSearch: React.Dispatch<React.SetStateAction<string>>;
    ValidateStudent: React.MouseEventHandler<HTMLButtonElement>;
    selectedStudentId: number | null;
    SelectStudent: (id: number) => void;
    student: StudentType | null;
    activeLesson: number | null;
    instUnit: InstUnitType; // Define appropriate type if possible
}

const StudentTabs: React.FC<StudentTabsProps> = ({
    studentGroups,
    currentPage,
    setCurrentPage,
    search,
    setSearch,
    ValidateStudent,
    selectedStudentId,
    student,
    SelectStudent,
    activeLesson,
    instUnit,
}) => {
    const [activeKey, setActiveKey] = React.useState("verified");
    
    const totalUnverified = studentGroups?.unverified?.total ?? 0;
    const totalVerified = studentGroups?.verified?.total ?? 0;
    const totalInactive = studentGroups?.inactive?.total ?? 0;
   
    useEffect(() => {
        let tabTotal: number = 0;
    
        // Conditionally assign tabTotal based on the presence of studentGroups
        if (studentGroups) {
            tabTotal = studentGroups.unverified.total;
        }
    
        // Set the active key based on whether there are unverified students
        setActiveKey(tabTotal > 0 ? "unverified" : "verified");
    }, [studentGroups]);

    const loadMoreStudents = () => {
        setCurrentPage(currentPage + 1);
        window.scrollTo(0, 0);
    };

    const loadPrevStudents = () => {
        setCurrentPage(currentPage - 1);
        window.scrollTo(0, 0);
    };

    if (!studentGroups) {
        return (
            <Alert variant="danger">
                <Alert.Heading>No Students</Alert.Heading>
                <p>There are no students in this tab.</p>
            </Alert>
        );
    }

    const handleSearchSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setCurrentPage(1);
    }

  
    return (
        <Tabs activeKey={activeKey} onSelect={(k) => setActiveKey(k)}>
            <Tab eventKey="unverified" title={`Unverified ${totalUnverified}`}>
                <UnverifiedStudentList
                    search={search}
                    studentGroups={studentGroups}
                    currentPage={currentPage}
                    loadPrevStudents={() => setCurrentPage(currentPage - 1)}
                    loadMoreStudents={() => setCurrentPage(currentPage + 1)}
                    handleSearchSubmit={handleSearchSubmit}
                    SelectStudent={SelectStudent}
                    selectedStudentId={selectedStudentId}
                    ValidateStudent={ValidateStudent}
                    activeLesson={activeLesson}
                    instUnit={instUnit}  
                    studentUnit={student?.studentUnit ?? null}
                    studentUnitLessons={student?.studentLessons ?? []}                  
                />
            </Tab>
            <Tab eventKey="verified" title={`Verified ${totalVerified}`}>
                {/* <VerifiedStudentList
                     studentGroups={studentGroups}
                     currentPage={currentPage}
                     loadPrevStudents={() => setCurrentPage(currentPage - 1)}
                     loadMoreStudents={() => setCurrentPage(currentPage + 1)}
                     search={search}
                     SelectStudent={SelectStudent}
                     selectedStudentId={selectedStudentId}
                     ValidateStudent={ValidateStudent}
                     activeLesson={activeLesson}
                     instUnit={instUnit} 
                /> */}
            </Tab>
            <Tab eventKey="inactive" title={`Inactive ${totalInactive}`}>
                {/* <InactiveStudentList
                     studentGroups={studentGroups}
                     currentPage={currentPage}
                     loadPrevStudents={() => setCurrentPage(currentPage - 1)}
                     loadMoreStudents={() => setCurrentPage(currentPage + 1)}
                     search={search}
                     SelectStudent={SelectStudent}
                     selectedStudentId={selectedStudentId}
                     ValidateStudent={ValidateStudent}
                     activeLesson={activeLesson}
                     instUnit={instUnit} 
                /> */}
            </Tab>
        </Tabs>
    );
};

export default StudentTabs;
