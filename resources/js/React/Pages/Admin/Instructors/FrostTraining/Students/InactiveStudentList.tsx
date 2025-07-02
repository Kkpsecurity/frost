import React from "react";
import StudentPaginator from "./StudentPaginator";
import PageCount from "./PageCount";
import StudentSearchBox from "./StudentSearchBox";
import StudentList from "./StudentList";
import { InstUnitType, StudentLessonType, StudentTabType, StudentType, StudentUnitType } from "../../../../../Config/types";


interface InactiveStudentListProps {
    studentGroups: StudentTabType;
    currentPage: number;
    loadPrevStudents: () => void;
    loadMoreStudents: () => void;
    search: string;
    handleSearchSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
    ValidateStudent: React.MouseEventHandler<HTMLButtonElement>;
    SelectStudent: (id: number) => void;
    selectedStudentId: number | null;
    activeLesson: number | null;
    instUnit: InstUnitType; // Define appropriate type if possible
    studentUnit: StudentUnitType;
    studentUnitLessons: StudentLessonType[];
}

const InactiveStudentList = ({
    studentGroups,
    studentUnit,
    studentUnitLessons,
    loadPrevStudents,
    currentPage,
    loadMoreStudents,
    search,
    handleSearchSubmit,
    ValidateStudent,
    SelectStudent,
    selectedStudentId,
    activeLesson,
    instUnit
}: InactiveStudentListProps) => {
    return (
        <>
            <div style={{ height: "calc(100vh)", overflowY: "scroll" }}>
                <div
                    className="page-metatdata"
                    style={{
                        position: "sticky",
                        top: 0,
                        zIndex: 100,
                        display: "flex",
                        justifyContent: "space-start",
                        alignItems: "center",
                        background: "#222",
                        height: "40px",
                        color: "#fff",
                        padding: "0 10px",
                    }}
                >
                    <PageCount
                        currentPage={currentPage}
                        lastPage={studentGroups.inactive.last_page}
                    />
                    <div className="col-8 text-end">
                        <StudentSearchBox
                            search={search}
                            handleSearchSubmit={handleSearchSubmit}
                        />
                    </div>
                </div>
                
                <StudentList
                    students={studentGroups.inactive.students}
                    studentUnit={studentUnit ?? null}
                    studentUnitLesson={studentUnitLessons ?? null}
                    ValidateStudent={ValidateStudent}
                    selectedStudentId={selectedStudentId}
                    SelectStudent={SelectStudent}
                    activeLesson={activeLesson}
                    instUnit={instUnit}                 
                />
            </div>
            
            <StudentPaginator
                loadPrevStudents={loadPrevStudents}
                currentPage={currentPage}
                loadMoreStudents={loadMoreStudents}
                TotalPages={studentGroups.inactive.total}
            />
        </>
    );
};

export default InactiveStudentList;
