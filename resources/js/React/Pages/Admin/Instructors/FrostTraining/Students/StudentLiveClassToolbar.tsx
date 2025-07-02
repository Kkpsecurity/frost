import React, { Dispatch, MouseEventHandler, SetStateAction, useContext, useState } from "react";
import { StudentLessonType, StudentType } from "../../../../../Config/types";

import useRevokeDNCHook from "../../../../../Hooks/Admin/useRevokeDNCHook";
import useAllowAccessHook from "../../../../../Hooks/Admin/useAllowAccessHook";
import useEjectStudentHook from "../../../../../Hooks/Admin/useEjectStudentHook";
import useBanStudentHook from "../../../../../Hooks/Admin/useBanStudentHook";

import AllowAccessButton from "../../../../../Hooks/Admin/AllowAccessButton";
import AllowAccessModal from "../../../../../Hooks/Admin/AllowAccessModal";

const StudentLiveClassToolbar = ({
    classData,
    student,
    studentLesson,
    activeLesson,
}) => {
    const { currentStudentUnit: studentUnit } = classData;
    if(!student) return null; // Return null if student or studentUnit is not available 

    /**
     * Eject Student from the Day
     */
    const {
        isStudentEjected,
        ejectLoading,
        showEjectModal,
        setShowEjectModal,
        setEjectReason,
        ConfirmEjectStudent,
        HandleEjectStudent,
        EjectStudentButton,
        EjectModal,
    } = useEjectStudentHook({ studentUnit });

    /**
     * Ban Student from Course
     */
    const {
        banLoading,
        isStudentBanned,
        BanModal,
        showBanModal,
        setShowBanModal,
        BanStudentButton,
        setBanLoading,
        HandleBanStudent,
    } = useBanStudentHook({ student });

    /**
     * Revoke DNC if Missed Challenges
     */
    const {
        isDNCed,
        DNCModal,
        dncLoading,
        HandleRevokeDNC,
        ConfirmRevokeDNC,
        RevokeDNCButton,
        showDNCModal,
        setShowDNCModal,
    } = useRevokeDNCHook({
        student,
        activeLesson,
        studentLesson,
    });

    type AllowAccessButtonProps = {
        isStudentLate: boolean;
        ConfirmAllowAccess: () => void;
        HandleAllowAccess: () => void;
        allowAccessLoading: boolean;
        AllowAccessModal: JSX.Element;
        showAllowAccessModal: boolean;
        setShowAllowAccessModal: () => void;
        setAllowAccessReason: Dispatch<SetStateAction<string | null>>;
    };

    const {
        isStudentLate,
        ConfirmAllowAccess,
        HandleAllowAccess,
        allowAccessLoading,
        showAllowAccessModal,
        setAllowAccessReason,
        setShowAllowAccessModal,
    } = useAllowAccessHook({
        student,
        studentUnit,
        activeLesson,
    }) as unknown as AllowAccessButtonProps; // Include the AllowAccessButtonProps type declaration

    return (
        <>
            <div className="d-flex p-3">
                {/* Eject Student From the Day */}
                <EjectStudentButton
                    classData={classData}
                    isStudentEjected={isStudentEjected}
                    ejectLoading={ejectLoading}
                    ConfirmEjectStudent={ConfirmEjectStudent}
                />

                {/* Ban From Course */}
                <BanStudentButton
                    student={student}
                    setShowBanModal={setShowBanModal}
                    isStudentBanned={isStudentBanned}
                    banLoading={banLoading}
                />

                {/* Revoke DNC Missed Challeages */}
                <RevokeDNCButton
                    student={student}
                    studentLesson={studentLesson}
                    activeLesson={activeLesson}
                    ConfirmRevokeDNC={ConfirmRevokeDNC}
                />

                {/* Missed Lesson Entry */}
                <AllowAccessButton
                    student={student}
                    studentLesson={studentLesson}
                    activeLesson={activeLesson}
                    ConfirmAllowAccess={ConfirmAllowAccess}
                    isStudentLate={isStudentLate}
                />
            </div>

            <EjectModal
                student={student}
                showEjectModal={showEjectModal}
                setShowEjectModal={setShowEjectModal}
                HandleEjectStudent={HandleEjectStudent}
                ejectLoading={ejectLoading}
                setEjectReason={setEjectReason}
                isStudentEjected={isStudentEjected}
                studentUnit={studentUnit}
            />

            <BanModal
                student={student}
                showBanModal={showBanModal}
                setShowBanModal={setShowBanModal}
                banLoading={banLoading}
                setBanLoading={setBanLoading}
                HandleBanStudent={HandleBanStudent}
            />

            <DNCModal
                student={student}
                showDNCModal={showDNCModal}
                studentLesson={studentLesson}
                dncLoading={dncLoading}
                setShowDNCModal={setShowDNCModal}
                HandleRevokeDNC={HandleRevokeDNC}
                activeLesson={activeLesson}
            />

            <AllowAccessModal
                student={student}
                showAllowAccessModal={showAllowAccessModal}
                setShowAllowAccessModal={setShowAllowAccessModal}
                setAllowAccessReason={setAllowAccessReason}
                HandleAllowAccess={HandleAllowAccess}
                allowAccessLoading={allowAccessLoading}
                studentUnitId={student?.studentUnit?.id}
            />
        </>
    );
};

export default StudentLiveClassToolbar;

// import Loader from "../../../../../Components/Widgets/Loader";

// import { ToastContainer, toast } from "react-toastify";
// import "react-toastify/dist/ReactToastify.css";
// import useBanStudentHook from "../../../../../Hooks/Admin/useBanStudentHook";
// import BanStudentButton from "../../../../../Hooks/Admin/BanStudentButton";
// import BanModal from "../../../../../Hooks/Admin/BanModal";
// import EjectStudentButton from "../../../../../Hooks/Admin/EjectStudentButton";

// const StudentLiveClassToolbar = ({ student, studentLesson, activeLesson }) => {

//     return (
//         <>
//             <div className="d-flex p-3">
//                 <ToastContainer />

//                 {/* Revoke DNC */}
//                 <RevokeDNCButton
//                     student={student}
//                     studentLesson={studentLesson}
//                     activeLesson={activeLesson}
//                     ConfirmRevokeDNC={ConfirmRevokeDNC}
//                     toast={toast}
//                 />

//                 <EjectStudentButton
//                     student={student}
//                     studentLesson={studentLesson}
//                     activeLesson={activeLesson}
//                     ConfirmEjectStudent={ConfirmEjectStudent}
//                 />

//                 {/* Missed Lesson Entry */}
//                 <AllowAccessButton
//                     student={student}
//                     studentLesson={studentLesson}
//                     activeLesson={activeLesson}
//                     ConfirmAllowAccess={ConfirmAllowAccess}
//                 />

//                 {/* Ban From Course */}
//                 <BanStudentButton
//                     student={student}
//                     setShowBanModal={setShowBanModal}
//                     isStudentBanned={isStudentBanned}
//                     banLoading={banLoading}
//                     toast={toast}
//                 />
//             </div>

//             <DNCModal
//                 student={student}
//                 showDNCModal={showDNCModal}
//                 studentLesson={studentLesson}
//                 dncLoading={dncLoading}
//                 setShowDNCModal={setShowDNCModal}
//                 HandleRevokeDNC={HandleRevokeDNC}
//                 activeLesson={activeLesson}
//             />

//             <AllowAccessModal
//                 student={student}
//                 showAllowAccessModal={showAllowAccessModal}
//                 setShowAllowAccessModal={setShowAllowAccessModal}
//                 setAllowAccessReason={setAllowAccessReason}
//                 HandleAllowAccess={HandleAllowAccess}
//                 allowAccessLoading={allowAccessLoading}
//                 studentUnitId={student?.studentUnit?.id}
//             />

//             <BanModal
//                 student={student}
//                 showBanModal={showBanModal}
//                 setShowBanModal={setShowBanModal}
//                 banLoading={banLoading}
//                 setBanLoading={setBanLoading}
//                 HandleBanStudent={HandleBanStudent}
//             />
//         </>
//     );
// };

// export default StudentLiveClassToolbar;

// // const [studentPresent, setStudentPresent] = useState<boolean>(false);
// // const [studentEjected, setStudentEjected] = useState<boolean>(false);
// // const [studentBanned, setStudentBanned] = useState<boolean>(false);

// // const [showAllowAccessModal, setShowAllowAccessModal] =
// //     useState<boolean>(false);
// // const [allowAccessReason, setAllowAccessReason] = useState<string | null>(
// //     null
// // );

// // const [showDNCModal, setShowDNCModal] = useState<boolean>(false);

// // const [showBanModal, setShowBanModal] = useState<boolean>(false);

// // if (!student) return <></>;
// // if (allowAccessLoading) return <Loader />;
