import React from "react";
import { ListGroup } from "react-bootstrap";

import FrostChatCard from "../../../../Components/Plugins/Frost/FrostAjaxChat/FrostChatCard";
import InstructorCard from "./Cards/InstructorCard";
import ActiveLessonCard from "./Cards/ActiveLessonCard";
import StudentRequirementsCard from "./Cards/StudentRequirementsCard";
import LessonDocumentsCard from "./LessonDocumentsCard";

const StudendDashboardWidgets = ({ laravel, data, darkMode, debug }) => {
    if(debug) console.log("Student Dashboard Widgets", data);


    return (
        <div>
            <ListGroup.Item className="mb-2 instrcutor-card">
                <InstructorCard
                    darkMode={darkMode} laravel={laravel} 
                    data={data} debug={debug} />
            </ListGroup.Item>
            
            <ListGroup.Item className="mb-2 chat-card">
                <FrostChatCard
                    course_date_id={data?.courseDate?.id}
                    isChatEnabled={data.isChatEnabled}
                    chatUser={{
                        user_id: laravel.user.id,
                        user_type: "student",
                        user_name: laravel.user.fname + " " + laravel.user.lname,
                        user_avatar: laravel.user.avatar,
                    }}
                    darkMode={darkMode}
                    debug={debug}
                />
            </ListGroup.Item>

            <ListGroup.Item className="mb-2 active-lesson-card">
                <ActiveLessonCard darkMode={darkMode} data={data} debug={debug} />
            </ListGroup.Item>
            
            <ListGroup.Item className="mb-2 image-preview-card">
                <StudentRequirementsCard
                    darkMode={darkMode}
                    data={data}
                    laravel={laravel}
                    debug={debug}
                />
            </ListGroup.Item>

            <ListGroup.Item className="mb-2 document-card">
                <LessonDocumentsCard data={data} darkMode={darkMode}/>
            </ListGroup.Item>
        </div>
    );
};

export default StudendDashboardWidgets;
