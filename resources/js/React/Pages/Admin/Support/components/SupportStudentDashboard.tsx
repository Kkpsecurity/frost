import React, { useState } from "react";
import StudentProfileCard from "./StudentProfileCard";
import AttendanceCard from "./AttendanceCard";
import SupportTabs from "./SupportTabs";

const SupportStudentDashboard = ({
    isActive,
    setIsActive,
    classData,
    debug = false,
}) => {
    if (classData === null || classData === undefined) {
        return;
    }

    const [selectedCourseId, setSelectedCourseId] = useState(null);
    const { student } = classData;

    console.log("SupportStudentDashboard", student);

    return (
        <section className="content">
            <div className="container-fluid">
                <div className="row">
                    <div className="col-md-3">
                        <StudentProfileCard student={student} debug={debug} />
                        <AttendanceCard
                            classData={classData}
                            selectedCourseId={selectedCourseId}
                        />
                    </div>
                    <div className="col-md-9">
                        <SupportTabs
                            isActive={isActive}
                            student={student}
                            classData={classData}
                            setSelectedCourseId={setSelectedCourseId}
                            selectedCourseId={selectedCourseId}
                        />
                    </div>
                </div>
            </div>
        </section>
    );
};

export default SupportStudentDashboard;
