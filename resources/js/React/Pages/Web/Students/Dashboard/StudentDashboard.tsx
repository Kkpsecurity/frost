/** @format */

import React, { useContext, useEffect, useState } from "react";
import {
  LaravelDataShape,
  StudentRequirementsType,
} from "../../../../Config/types";
import { ClassContext } from "../../../../Context/ClassContext";
import { useDetermineView } from "../Partials/determineView";
import OfflineClassRoom from "./Rooms/Offline/OfflineClassRoom";
import StudentAgreement from "./Rooms/Verification/StudentAgreement";
import ClassRules from "./Rooms/Verification/ClassRules";
import PageLoader from "../../../../Components/Widgets/PageLoader";
import { Alert } from "react-bootstrap";
import VirtualClassRoom from "./Rooms/VirtualClassRoom";
import PendingVerification from "./Rooms/Verification/PendingVerification";
import WaitingRoom from "./Rooms/Waiting/WaitingRoom";
import DNCStudent from "./Rooms/Banned/DNCStudent";
import StudentEjected from "./StudentEjected";
import StudentBanned from "./StudentBanned";

interface StudentDashboardProps {
  laravel: LaravelDataShape;
  darkMode: boolean;
  debug?: boolean;
}

const classroomStates = {
  OfflineClassRoom: "offline-classroom",
  StudentAgreement: "agreement-required",
  ClassRules: "class-rules",
  VirtualClassRoom: "virtual-class",
  WaitingRoom: "waiting-room",
  PendingVerification: "pending-verification",
  StudentBanned: "student-disabled",
  StudentEjected: "student-ejected",
};

const StudentDashboard: React.FC<StudentDashboardProps> = ({
  darkMode,
  laravel,
  debug = false,
}) => {
  if (debug) console.log("Student Dashboard Initialized", laravel, darkMode);
  const [activeView, setActiveView] = useState("offline-classroom");
  const [isLoading, setIsLoading] = useState(false);

  let studentRequirementDefaults: StudentRequirementsType = {
    classRulesAgreement: {
      agreedToRules: "2021-01-01",
    },
    identityVerification: {
      headshot: "",
      idcard: "",
    },
    studentAgreement: {
      agreed: false,
    },
  };

  const [studentRequirements, setStudentRequirements] = useState(
    studentRequirementDefaults
  );

  const handleRuleAgreement = async () => {
    console.log("handleRuleAgreement - Start");
    setIsLoading(true);
    window.scrollTo(0, 0);

    const today = new Date();
    const dateString = today.toISOString().split("T")[0];

    try {
      localStorage.setItem("agreedToRules", dateString);
      setStudentRequirements((prevState) => ({
        ...prevState,
        classRulesAgreement: {
          agreedToRules: dateString,
        },
      }));
    } catch (error) {
      console.error("Error setting local storage:", error);
    } finally {
      setIsLoading(false);
    }

    console.log("handleRuleAgreement - End");
  };

  const ClassRoomData = useContext(ClassContext);

  const views = {
    [classroomStates.OfflineClassRoom]: (
      <OfflineClassRoom
        darkMode={darkMode}
        data={ClassRoomData}
        student={laravel.user}
        debug={debug}
      />
    ),
    [classroomStates.StudentBanned]: <StudentBanned />,
    [classroomStates.StudentAgreement]: (
      <StudentAgreement
        student={laravel.user}
        course={ClassRoomData?.course ?? null}
      />
    ),
    [classroomStates.ClassRules]: <ClassRules onAgree={handleRuleAgreement} />,
    [classroomStates.WaitingRoom]: (
      <WaitingRoom student={laravel.user} debug={debug} />
    ),
    [classroomStates.PendingVerification]: (
      <PendingVerification
        data={ClassRoomData}
        student={laravel.user}
        validations={laravel.user.validations}
        debug={debug}
      />
    ),
    [classroomStates.StudentEjected]: (
      <StudentEjected classData={ClassRoomData} />
    ),
    [classroomStates.VirtualClassRoom]: (
      <VirtualClassRoom darkMode={darkMode} laravel={laravel} debug={debug} />
    ),
  };

  const dview = useDetermineView({
    laravel,
    studentRequirements,
    setStudentRequirements,
    debug,
  });

  useEffect(() => {
    const getView = async () => {
      setActiveView(dview as string);
    };

    getView();
  }, [dview, ClassRoomData, laravel, studentRequirements]);

  const ActiveViewComponent = views[activeView] || (
    <Alert variant="danger">Invalid View</Alert>
  );

  if (isLoading) return <PageLoader base_url={window.location.origin} />;

  return <React.Fragment>{ActiveViewComponent}</React.Fragment>;
};

export default StudentDashboard;
