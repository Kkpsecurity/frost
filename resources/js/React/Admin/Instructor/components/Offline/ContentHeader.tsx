import React from "react";

interface ContentHeaderProps {
    title?: string;
    courseCount: number;
}

const ContentHeader: React.FC<ContentHeaderProps> = ({
    title = "Current Courses for the Day",
    courseCount
}) => {
    return null;
};

export default ContentHeader;
