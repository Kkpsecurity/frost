import React from "react";
import { ClassDataShape, StudentType } from "../../../../../Config/types";

import DocumentsStudyRoom from "./Docs/DocumentsStudyRoom";
import ExaminationRoom from "./Exams/ExaminationRoom";
import OffLineDashboard from "./Offline/OffLineDashboard";
import VideosStudyRoom from "./Videos/VideosStudyRoom";

interface Props {
    data: ClassDataShape;
    student: StudentType;
    section: string;
    selectedLessonId: number | null;
    offlineSessionStarted: boolean | null;
    createTrackLesson: (e) => void;
    debug: boolean;
}

const components = {
    "home-nav-tab": OffLineDashboard,
    "videos-nav-tab": VideosStudyRoom,
    "documents-nav-tab": DocumentsStudyRoom,
};

const ClassRoomActivities: React.FC<Props> = ({
    data,
    student,
    section,
    selectedLessonId,
    offlineSessionStarted,
    createTrackLesson,
    debug,
}) => {
    const Component = components[section] || OffLineDashboard;
    return <Component data={data} student={student} debug={debug} />;
};

export default ClassRoomActivities;
