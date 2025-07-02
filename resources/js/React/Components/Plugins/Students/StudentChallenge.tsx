/** @format */

import React, { useState, useEffect } from "react";
import { Modal } from "react-bootstrap";
import apiClient from "../../../Config/axios";
import {
  ClassDataShape,
  StudentType,
  CourseLessonType,
  ChallengeType,
} from "../../../Config/types";
import ModalChallengeForm from "./ModalChallengeForm";

interface StudentProps {
  student: StudentType;
  classData: ClassDataShape;
}

const StudentChallenge: React.FC<StudentProps> = ({ student, classData }) => {
  const { courseLessons, instUnitLesson, challenge, previousLessons } =
    classData;

  const [show, setShow] = useState(false);
  const [challengeReady, setChallengeReady] = useState(false);
  const [isFinal, setIsFinal] = useState(false);
  const [isCurrentLessonEnded, setIsCurrentLessonEnded] = useState(false);
  const [isPreviousLesson, setIsPreviousLesson] = useState(false);
  const [challengeData, setChallengeData] = useState<ChallengeType>();
  const [previousLesson, setPreviousLesson] = useState<CourseLessonType>();
  const [challengeTimerId, setChallengeTimerId] = useState<NodeJS.Timeout>();

  const verifyCode = () => {
    const data = {
      challenge_id: challengeData?.challenge_id,
      student_id: student.id,
    };

    apiClient
      .post("/services/challenge/verify", data)
      .then((response) => {
        console.log("challenge verified", response.data);
      })
      .catch((error) => {
        console.error("error verifying challenge", error);
        alert("There was an error verifying the challenge.");
      });

    setShow(false);
    setIsFinal(false);
    setIsCurrentLessonEnded(false);
  };

  const triggerChallengeTimedOut = (challenge: ChallengeType) => {
    const data = {
      challenge_id: challenge.challenge_id,
      student_id: student.id,
    };

    apiClient
      .post("/services/challenge/timed-out", data)
      .then((response) => {
        console.log("timed out", response.data);
      })
      .catch((error) => {
        console.error("error triggering challenge timeout", error);
        alert("There was an error triggering the challenge timeout.");
      });

    setShow(false);
    setIsFinal(false);
    setIsCurrentLessonEnded(false);
  };

  useEffect(() => {
    if (!classData) {
      return;
    }

    const courseLessonsArray = Object.values(courseLessons);
    const activeLesson = courseLessonsArray.find(
      (lesson) => lesson.id === instUnitLesson?.lesson_id
    );

    if (activeLesson) {
      setIsPreviousLesson(previousLessons.includes(activeLesson.id));
      localStorage.setItem("activeLesson", JSON.stringify(activeLesson));
    } else {
      console.log("No active lesson found!");
    }
  }, [classData, instUnitLesson]);

  useEffect(() => {
    if (!classData.challenge) {
      return;
    }

    const exitFullScreen = async () => {
      window.postMessage("exitFullscreen", "*");
      console.log("***  Sending exitFullscreen  ***");
    };

    if (classData.challenge.isChallengeEOLReady === true) {
      setChallengeData(classData.challenge);
      setIsCurrentLessonEnded(true);
      exitFullScreen();
      setShow(true);
    }

    if (classData.challenge.isChallengeReady === true) {
      setChallengeData(classData.challenge);
      localStorage.setItem(
        "lastChallenge",
        JSON.stringify(classData.challenge)
      );
      setChallengeReady(true);
      exitFullScreen();
      setShow(true);

      if (classData.challenge.is_final === true) {
        setIsFinal(true);
      }
    }
  }, [classData.challenge]);

  useEffect(() => {
    if (show === true && challengeData) {
      const timerId = setTimeout(() => {
        setShow(false);
        triggerChallengeTimedOut(challengeData);
      }, parseInt(challengeData.challenge_time, 10) * 1000);

      setChallengeTimerId(timerId);
    } else {
      setChallengeTimerId(undefined);
    }

    return () => {
      if (challengeTimerId) {
        clearTimeout(challengeTimerId);
      }
    };
  }, [show, challengeData]);

  useEffect(() => {
    if (!show && challengeTimerId) {
      clearTimeout(challengeTimerId);
      setChallengeTimerId(undefined);
    }
  }, [show]);

  useEffect(() => {
    const lastChallenge = localStorage.getItem("lastChallenge");
    if (lastChallenge) {
      setChallengeData(JSON.parse(lastChallenge));
    }
  }, []);

  if (show === false) return <></>;

  const DisplayLessonStatus = ({ isFinal, isCurrentLessonEnded }) => {
    return (
      <>
        <span className="title" style={{ width: "50%" }}>
          Student Challenge
        </span>

        {isFinal && (
          <span className="lead fw-bold p-2 ml-2" style={{ width: "50%" }}>
            Final Attempted
          </span>
        )}

        {isCurrentLessonEnded && (
          <span className="lead fw-bold" style={{ width: "50%" }}>
            Lesson Complete
          </span>
        )}
      </>
    );
  };

  return (
    <Modal
      show={show}
      centered={true}
      className=""
      style={{ zIndex: 9999, left: "25%" }}>
      <Modal.Header>
        <Modal.Title>
          <div className="row">
            <div className="col-12 d-flex align-items-center justify-content-between">
              <DisplayLessonStatus
                isFinal={isFinal}
                isCurrentLessonEnded={isCurrentLessonEnded}
              />
            </div>
          </div>
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {challengeReady ? (
          <ModalChallengeForm
            verifyCode={verifyCode}
            isFinal={isFinal}
            isEOL={isCurrentLessonEnded}
          />
        ) : (
          isCurrentLessonEnded && (
            <ModalChallengeForm
              verifyCode={verifyCode}
              isFinal={isFinal}
              isEOL={isCurrentLessonEnded}
            />
          )
        )}
      </Modal.Body>
    </Modal>
  );
};

export default StudentChallenge;
