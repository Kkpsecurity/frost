import React, { useState, useEffect, useRef } from "react";
import {
    ClassDataShape,
    StudentType,
    StudentExamType,
} from "../../../../../../Config/types";

interface Props {
    data: ClassDataShape;
    student: StudentType;
    debug: boolean;
}

const ExaminationRoom: React.FC<Props> = ({ data, student, debug = false }) => {
  return (
    <div>ExaminationRoom</div>
  )
}

export default ExaminationRoom




// import React, { useState, useEffect, useRef } from "react";
// import {
//     ClassDataShape,
//     StudentType,
//     StudentExamType,
// } from "../../../../../../Config/types";

// interface Props {
//     data: ClassDataShape;
//     student: StudentType;
//     debug: boolean;
// }

// const ExaminationRoom: React.FC<Props> = ({ data, student, debug = false }) => {
//     const [checkedState, setCheckedState] = useState({});
//     const divRef = useRef();
//     const { studentExam } = data;

//     // get base url
//     const url = window.location.origin;

//     // if exam is not null the exam-room id container should be fullscreen
//     useEffect(() => {
//         const goFullScreen = () => {
//             if (divRef.current) {
//                 // @ts-ignore
//                 if (divRef.current.requestFullscreen) {
//                     // @ts-ignore
//                     divRef.current.requestFullscreen();

//                     // @ts-ignore
//                 } else if (divRef.current.mozRequestFullScreen) {
//                     // @ts-ignore
//                     divRef.current.mozRequestFullScreen();

//                     // @ts-ignore
//                 } else if (divRef.current.webkitRequestFullscreen) {
//                     // @ts-ignore
//                     divRef.current.webkitRequestFullscreen();

//                     // @ts-ignore
//                 } else if (divRef.current.msRequestFullscreen) {
//                     // @ts-ignore
//                     divRef.current.msRequestFullscreen();
//                 }
//             }
//         };

//         if (studentExam) {
//             // goFullScreen();
//         }
//     }, [studentExam]);

//     useEffect(() => {
//         let initialState = {};
//         // TODO: Update this section to correctly iterate over questions and answers
//         // based on the actual structure of your exam data
//         setCheckedState(initialState);
//     }, [studentExam]);

//     console.log("ExaminationRoom: ", studentExam);

//     return (
//         <div
//             className="container"
//             ref={divRef}
//             style={{ width: "100%", height: "100%", background: "#111" }}
//         >
//             <div className="row">
//                 <div className="col-12">
//                     <div className="mt-5">
//                         <h1 className="text-white">The Examination Room</h1>
//                         {studentExam && (
//                             <a className="btn btn-success" href={`${url}/classroom/exam/${studentExam.id}`}>
//                                 Take Exam
//                             </a>
//                         )}
//                     </div>
//                 </div>
//             </div>
//         </div>
//     );
// };

// export default ExaminationRoom;
