import React from "react";
import { t } from "@/i18n";
import OfflineTabsQuickStats from "../OfflineTabsQuickStats";
import { useStudent } from "../../context/StudentContext";
import StudentProfileCard from "./StudentProfileCard";
import CourseDetailsCard from "./CourseDetailsCard";
import IdCardSection from "./IdCardSection";
import SignaturesSection from "./SignaturesSection";

type OfflineLessonLike = {
    is_completed?: boolean;
    duration_minutes?: number;
};

interface TabDetailsProps {
    courseAuthId: number;
    lessons: OfflineLessonLike[];
}

const mutedText: React.CSSProperties = { color: "#95a5a6" };

const TabDetails: React.FC<TabDetailsProps> = ({ courseAuthId, lessons }) => {
    const student = useStudent();

    const validations = student?.validationsByCourseAuth
        ? student.validationsByCourseAuth[courseAuthId]
        : null;

    const completedCount = React.useMemo(
        () => lessons.filter((l) => l.is_completed).length,
        [lessons],
    );

    const selectedCourse = React.useMemo(() => {
        const courses = student?.courses ?? [];
        return (
            courses.find((c: any) => {
                const candidateCourseAuthId =
                    c?.course_auth_id ?? c?.courseAuthId ?? c?.id;
                return Number(candidateCourseAuthId) === Number(courseAuthId);
            }) ?? null
        );
    }, [student?.courses, courseAuthId]);

    const courseName =
        (selectedCourse as any)?.course_name ||
        (selectedCourse as any)?.courseName ||
        (selectedCourse as any)?.name ||
        (selectedCourse as any)?.course?.name ||
        "â€”";

    const studentDisplayName = React.useMemo(() => {
        const s: any = student?.student ?? null;
        if (!s) return "â€”";
        if (typeof s.name === "string" && s.name.trim()) return s.name;
        if (typeof s.full_name === "string" && s.full_name.trim())
            return s.full_name;
        if (typeof s.fullName === "string" && s.fullName.trim())
            return s.fullName;
        const first =
            (typeof s.fname === "string" ? s.fname : "") ||
            (typeof s.first_name === "string" ? s.first_name : "") ||
            (typeof s.firstName === "string" ? s.firstName : "") ||
            (typeof s.first === "string" ? s.first : "") ||
            "";
        const last =
            (typeof s.lname === "string" ? s.lname : "") ||
            (typeof s.last_name === "string" ? s.last_name : "") ||
            (typeof s.lastName === "string" ? s.lastName : "") ||
            (typeof s.last === "string" ? s.last : "") ||
            "";
        return `${first} ${last}`.trim() || "â€”";
    }, [student?.student]);

    const studentEmail = React.useMemo(() => {
        const s: any = student?.student ?? null;
        return s?.email || s?.user?.email || s?.username || "â€”";
    }, [student?.student]);

    return (
        <div className="details-tab">
            <h4
                className="mb-2"
                style={{
                    color: "white",
                    fontSize: "1.75rem",
                    fontWeight: "600",
                }}
            >
                <i
                    className="fas fa-tachometer-alt me-2"
                    style={{ color: "#3498db" }}
                ></i>
                {t("offlineTab.learningDashboard")}
            </h4>

            <p className="mb-4" style={mutedText}>
                {t("offlineTab.learningDashboardSubtitle")}
            </p>

            <OfflineTabsQuickStats lessons={lessons} />

            <div className="row g-3 vh-100">
                <div className="col-12 col-lg-5 ">
                    <StudentProfileCard
                        studentDisplayName={studentDisplayName}
                        studentEmail={studentEmail}
                        completedCount={completedCount}
                        lessonsTotal={lessons.length}
                    />
                </div>

                <div className="col-12 col-lg-7">
                    <CourseDetailsCard
                        courseName={courseName}
                        completedCount={completedCount}
                        lessonsTotal={lessons.length}
                    />
                </div>

                <div className="col-12 col-lg-6 vh-100">
                    <IdCardSection
                        validations={validations}
                        student={student?.student ?? null}
                        courseAuthId={courseAuthId}
                    />
                </div>

                <div className="col-12 col-lg-6">
                    <SignaturesSection
                        courseAuthId={courseAuthId}
                        studentId={(student?.student as any)?.id ?? null}
                        existingSignatureUrl={(validations as any)?.signature ?? null}
                    />
                </div>
            </div>
        </div>
    );
};

export default TabDetails;
