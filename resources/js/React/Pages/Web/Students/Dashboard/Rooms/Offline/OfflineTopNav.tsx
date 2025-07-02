import React from "react";
import { colors } from "../../../../../../Config/colors";
import { ClassDataShape, StudentType } from "../../../../../../Config/types";

import {
    Menu,
    MenuItem,
    MenuBorder,
} from "../../../../../../Styles/styledOfflineNavMenu.styled";

interface OfflineTopNavProps {
    darkMode: boolean;
    data: ClassDataShape;
    student: StudentType;
    section: string;
    setSection: (section: string) => void;
    debug: boolean;
}

const OfflineTopNav: React.FC<OfflineTopNavProps> = ({
    darkMode,
    data,
    student,
    section,
    setSection,
    debug = false,
}) => {
    const colorPalette = (darkMode) => (darkMode ? colors.dark : colors.light);

    const menuItems = [
        {
            id: "home-nav-tab",
            label: "Home",
            active: section === "home-nav-tab",
            bgColor: colorPalette(darkMode).listItemTabBgColor,
        },
        {
            id: "videos-nav-tab",
            label: "Videos",
            active: section === "videos-nav-tab",
            bgColor: colorPalette(darkMode).listItemTabBgColor,
        },
        {
            id: "documents-nav-tab",
            label: "Documents",
            active: section === "documents-nav-tab",
            bgColor: colorPalette(darkMode).listItemTabBgColor,
        },
    ];

    return (
        <Menu>
            {menuItems.map((item) => (
                <MenuItem
                    key={item.id}
                    className={(item.active ? "active " : " ") + item.id}
                    bgColor={item.bgColor}
                    onClick={() => setSection(item.id)}
                >
                    {item.label}
                </MenuItem>
            ))}
            <MenuBorder />
        </Menu>
    );
};

export default OfflineTopNav;
