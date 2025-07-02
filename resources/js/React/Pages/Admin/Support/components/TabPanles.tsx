import React, { useEffect } from "react";
import StudentActivity from "./StudentActivity";
import StudentCourseLessons from "./StudentCourseLessons";
import LiveClassSupport from "./LiveClassSupport";
import StudentPhotos from "./StudentPhotos";
import StudentDetails from "./StudentDetails";
import { StudentType } from "../../../../Config/types";
import ExamResult from "./ExamResult";

interface TabPanelsProps {
    classData: any;
    student: StudentType;
    selectedCourseId: number;
    setSelectedCourseId: any;
    activeLesson: any;
    selectedTab: any;
    setSelectedTab: any;
}

const TabPanels = ({
    classData,
    student,
    selectedCourseId,
    setSelectedCourseId,
    activeLesson,
    selectedTab,
    setSelectedTab,
}: TabPanelsProps) => {
    useEffect(() => {
        const tab = selectedTab.replace("#", "");
        setSelectedTab(tab);
    }, [selectedTab]);

    // Need the courseAuthID to get the correct course
    const courseAuthId = classData?.courseAuths[selectedCourseId]

    return (
        <>
            <div className="tab-content">
                <div
                    className={`tab-pane ${
                        selectedTab === "activity" ? "active" : ""
                    }`}
                    id="activity"
                >
                    <StudentActivity
                        classData={classData}
                        selectedCourseId={selectedCourseId}
                        setSelectedCourseId={setSelectedCourseId}
                    />
                </div>
                <div
                    className={`tab-pane ${
                        selectedTab === "lessons" ? "active" : ""
                    }`}
                    id="lessons"
                >
                    <StudentCourseLessons
                        classData={classData}
                        selectedCourseId={selectedCourseId}
                    />
                </div>
                <div
                    className={`tab-pane ${
                        selectedTab === "liveclass" ? "active" : ""
                    }`}
                    id="liveclass"
                >
                    <LiveClassSupport
                        classData={classData}
                        activeLesson={activeLesson}
                        selectedCourseId={selectedCourseId}
                    />
                </div>
                <div
                    className={`tab-pane ${
                        selectedTab === "photos" ? "active" : ""
                    }`}
                    id="photos"
                >
                    <StudentPhotos
                        student={student}
                        classData={classData}
                        selectedCourseId={selectedCourseId}
                    />
                </div>
                <div
                    className={`tab-pane ${
                        selectedTab === "exam" ? "active" : ""
                    }`}
                    id="exam"
                >
                   <ExamResult student={student} classData={classData} />
                </div>
                <div
                    className={`tab-pane ${
                        selectedTab === "details" ? "active" : ""
                    }`}
                    id="details"
                >
                    <StudentDetails student={student} classData={classData} />
                </div>
            </div>
        </>
    );
};

export default TabPanels;
