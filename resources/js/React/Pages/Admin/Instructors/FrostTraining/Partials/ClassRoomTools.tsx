import React from "react";
import {
    CourseMeetingShape,
    LaravelAdminShape,
} from "../../../../../Config/types";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
    faCheck,
    faPause,
    faSignOutAlt,
} from "@fortawesome/free-solid-svg-icons";

interface Props {
    data: CourseMeetingShape;
    laravel: LaravelAdminShape;
    leaveClass: (e) => Promise<void>;
    activeLesson: number | null;
    isPaused: boolean;
    pauseLesson: Function;
    completeBtnDisableTimer: boolean;
    markLessonComplete: Function;
    MarkDayCompletedButton: any;
}

const ClassRoomTools = ({
    data,
    laravel,
    leaveClass,
    activeLesson,
    isPaused,
    pauseLesson,
    completeBtnDisableTimer,
    markLessonComplete,
    MarkDayCompletedButton,
}: Props) => {
    return (
        <div className="collapse navbar-collapse" id="navbarSupportedContent">
            <ul className="navbar-nav ml-auto">
                {/* 
                    Button for assistants to leave the class
                */}
                {data.instUnit.assistant_id &&
                data.instUnit.assistant_id === laravel.user.id ? (
                    <li className="nav-item">
                        <button
                            id={String(data.courseDate.id)}
                            className="btn btn-sm btn-warning nav-link"
                            onClick={(e) => leaveClass(e)}
                        >
                            <FontAwesomeIcon icon={faSignOutAlt} /> Leave Class
                        </button>
                    </li>
                ) : null}

                {/* Only Instructor can pause the class */}
                {(!data.instUnit.assistant_id ||
                    data.instUnit.assistant_id !== laravel.user.id) &&
                    activeLesson !== null &&
                    activeLesson > 0 && (
                        <li className="nav-item">
                            <a
                                className="btn btn-sm ml-1 btn-info nav-link"
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault(); // Prevent default anchor behavior
                                    pauseLesson(
                                        !isPaused ? "true" : "false"
                                    );
                                }}
                                data-id={activeLesson}
                            >
                                <FontAwesomeIcon icon={faPause} /> Pause Lesson
                            </a>
                        </li>
                    )}

                {(!data.instUnit.assistant_id ||
                    data.instUnit.assistant_id !== laravel.user.id) && (
                    <li className="nav-item">
                        {activeLesson !== null && activeLesson > 0 ? (
                            <a
                                className={`btn btn-sm btn-danger ml-1 nav-link ${
                                    completeBtnDisableTimer ? "disabled" : ""
                                }`}
                                href="#"
                                onClick={(e) => markLessonComplete(e)}
                                data-id={activeLesson}
                            >
                                <FontAwesomeIcon icon={faCheck} /> Complete
                                Lesson
                            </a>
                        ) : (
                            <div className="btn-sm btn-warning text-dark nav-link ml-1">
                                {data ? (
                                    <MarkDayCompletedButton />
                                ) : (
                                    "No Lesson Completed"
                                )}
                            </div>
                        )}
                    </li>
                )}
            </ul>
        </div>
    );
};

export default ClassRoomTools;
