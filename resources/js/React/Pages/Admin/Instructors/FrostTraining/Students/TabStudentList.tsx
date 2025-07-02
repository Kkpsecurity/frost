import React from "react";
import StudentPaginator from "./StudentPaginator";
import PageCount from "./PageCount";
import StudentSearchBox from "./StudentSearchBox";
import StudentList from "./StudentList";
import { StudentTabType, StudentUnitType } from "../../../../../Config/types";

interface TabStudentListProps {
  studentGroups: StudentTabType; // You might need more detailed types
  studentUnit: StudentUnitType;
  studentUnitLesson: any;
  loadPrevStudents: () => void;
  currentPage: number;
  loadMoreStudents: () => void;
  search: string;
  handleSearchSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
  SelectStudent: (id: number) => void;
  selectedStudentId: number | null;
  ValidateStudent: React.MouseEventHandler<HTMLButtonElement>;
  activeLesson: number | null;
  instUnit: any;
  status: "verified" | "unverified" | "inactive"; // Add appropriate status values
}

const TabStudentList: React.FC<TabStudentListProps> = ({
  studentGroups,
  studentUnit,
  studentUnitLesson,
  loadPrevStudents,
  currentPage,
  loadMoreStudents,
  search,
  handleSearchSubmit,
  SelectStudent,
  selectedStudentId,
  ValidateStudent,
  activeLesson,
  instUnit,
  status,
}) => {
  const students = studentGroups?.[status]?.students || [];
  const lastPage = studentGroups?.[status]?.last_page || 1;

  return (
    <>
      <div style={{ height: "calc(100vh)", overflowY: "scroll" }}>
        <div
          className="page-metadata"
          style={{
            position: "sticky",
            top: 0,
            zIndex: 100,
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            background: "#222",
            height: "40px",
            color: "#fff",
            padding: "0 10px",
          }}
        >
          <PageCount currentPage={currentPage} lastPage={lastPage} />
          <StudentSearchBox search={search} handleSearchSubmit={handleSearchSubmit} />
        </div>

        <StudentList
          students={students}
          studentUnit={studentUnit}
          studentUnitLesson={studentUnitLesson}
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
        TotalPages={lastPage}
      />
    </>
  );
};

export default TabStudentList;
