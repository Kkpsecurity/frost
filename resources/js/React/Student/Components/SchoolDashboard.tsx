import React from "react";
import SchoolDashboardTitleBar from "./ShcoolDashboardTitleBar";
import SchoolNavBar from "./SchoolNavBar";
import SchoolDashboardTabContent from "./SchoolDashboardTabContent";
import { SchoolDashboardProps } from "../types/props/classroom.props";

const SchoolDashboard: React.FC<SchoolDashboardProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    return <div className="dashboard-content"></div>;
};

export default SchoolDashboard;
