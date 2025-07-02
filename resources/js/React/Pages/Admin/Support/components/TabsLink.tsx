import React from "react";
import { Nav } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import {
    faUserCircle,
    faBookOpen,
    faCamera,
    faBan,
    faUser,
    faWifi,
    faCodeCommit,
} from "@fortawesome/free-solid-svg-icons";

const TabsLink = ({
    classData,
    selectedCourseId,
    selectedTab,
    setSelectedTab,
}) => {
    const tabs = [
        { id: "#activity", icon: faUserCircle, label: "Activity" },
        {
            id: "#lessons",
            icon: faBookOpen,
            label: "Lessons",
            requireCourseId: true,
        },
        {
            id: "#liveclass",
            icon: classData.isClassLive ? faWifi : faBan,
            label: "Class History",
            requireCourseId: true,
            isLive: classData.isClassLive,
        },
        {
            id: "#photos",
            icon: faCamera,
            label: "Student Photos",
            requireCourseId: true,
        },
        {
            id: "#exam",
            icon: faCodeCommit,
            label: "Exam Results",
            requireCourseId: true,
        },
        {
            id: "#details",
            icon: faUser,
            label: "Student Details",
            requireCourseId: true,
        },
    ];

    return (
        <Nav
            variant="pills"
            activeKey={selectedTab}
            onSelect={setSelectedTab}
            defaultActiveKey="#activity"
        >
            {tabs.map((tab) => (
                <Nav.Item
                    key={tab.id}
                    className={tab.isLive ? "bg-success" : "bg-light"}
                >
                    <Nav.Link
                        href={
                            selectedCourseId || !tab.requireCourseId
                                ? tab.id
                                : "#"
                        }
                        eventKey={tab.id}
                        onClick={(e) => {
                            e.preventDefault(); // Prevent the default anchor behavior
                            window.scrollTo(0, 0); // Scroll to the top of the page
                            setSelectedTab(tab.id); // Update the selected tab state
                        }}
                        disabled={!selectedCourseId && tab.requireCourseId} // Disable link if courseId is required but not present
                        className={selectedTab === tab.id ? "active" : ""}
                    >
                        <FontAwesomeIcon icon={tab.icon} /> {tab.label}
                    </Nav.Link>
                </Nav.Item>
            ))}
        </Nav>
    );
};

export default TabsLink;
