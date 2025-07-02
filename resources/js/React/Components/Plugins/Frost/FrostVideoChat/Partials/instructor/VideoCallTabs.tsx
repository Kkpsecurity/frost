import React, { useEffect } from "react";
import { Tab, Tabs } from "react-bootstrap";
import ActiveStudentCallList from "./ActiveStudentCallList";
import VideoCallQueue from "./VideoCallQueue";

const VideoCallTabs = ({
    activeTab,
    setActiveTab,
    activeCallRequest,
    allStudents,
    callStudentId,
    acceptUserId,
    course_date_id,
    handleCallStudent,
    callHasEnded,
    handleEndCall,
    allQueueStudents,
    laravel,
}) => {
    useEffect(() => {
        if (callHasEnded) {
            setActiveTab("students");
        }
    }, [callHasEnded]);

    useEffect(() => {
        if(allQueueStudents.length > 0) {
            setActiveTab('queue');
        }
    }, [allQueueStudents]);
    
    return (
        <Tabs activeKey={activeTab} onSelect={(tabKey) => setActiveTab(tabKey)}>
            <Tab
                eventKey="students"
                title="Students"
                disabled={activeCallRequest}
            >
                <div
                    className="tab-contents"
                    style={{
                        height: "280px",
                        padding: 0,
                        margin: 0,
                    }}
                >
                    <ActiveStudentCallList
                        allStudents={allStudents}
                        callStudentId={callStudentId}
                        acceptUserId={acceptUserId}
                        courseDateId={course_date_id}
                        handleCallStudent={handleCallStudent}
                        handleEndCall={handleEndCall}
                        callHasEnded={callHasEnded}
                        laravel={laravel}
                    />
                </div>
            </Tab>
            <Tab eventKey="queue" title="Queued" disabled={activeCallRequest}>
                <div
                    className="tab-contents"
                    style={{
                        height: "280px",
                        padding: 0,
                        margin: 0,
                    }}
                >
                    <VideoCallQueue
                        queuedStudents={allQueueStudents}
                        callStudentId={callStudentId}
                        acceptUserId={acceptUserId}
                        courseDateId={course_date_id}
                        handleCallStudent={handleCallStudent}
                        callHasEnded={callHasEnded}
                        handleEndCall={handleEndCall}
                        laravel={laravel}
                    />
                </div>
            </Tab>
        </Tabs>
    );
};

export default VideoCallTabs;
