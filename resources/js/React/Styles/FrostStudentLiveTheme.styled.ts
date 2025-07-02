import styled from "styled-components";
import { colors } from "../Config/colors";

const sidebarWidth = "320px";
const sidebarCollapsedWidth = "50px";
const sidebarCollapsedWidthMobile = "0px";

const contentWidthFull = `calc(100% - ${sidebarWidth})`;
const contentWidthCollapsed = `calc(100% - ${sidebarCollapsedWidth})`;

interface FrostStudentOfflineThemeProps {
    darkMode: boolean;
}

function colorPalette(darkMode: boolean) {
    return darkMode ? colors.dark : colors.light;
}

const FrostStudentLiveTheme = styled.section`
    display: flex;
    flex-direction: column; /* keeps the content full width */
    background-color: ${({ darkMode }) => colorPalette(darkMode).mainBgColor};
    min-height: 100%;
    height: auto;

    .frost-sidebar {
        position: relative;
        width: ${(props) =>
            props.sidebarVisible ? sidebarWidth : sidebarCollapsedWidth};
        background-color: ${({ darkMode }) =>
            colorPalette(darkMode).sidebarBgColor};
        transition: width 0.3s ease;
        float: left;

        .nav-item.nav-item-sm {
            height: 60px;
            background-color: ${(props) =>
                colorPalette(props.darkMode).listItemTabBgColor} !important;
        }
    }

    .frost-content {
        position: relative;
        display: block;
        min-height: 100%;
        padding: 20px;
        height: auto;
        max-height: vh100;
        vertical-align: top;
        width: ${(props) =>
            props.sidebarVisible ? contentWidthFull : contentWidthCollapsed};
        background-color: ${({ darkMode }) =>
            colorPalette(darkMode).contentBgColor};
        transition: width 0.3s ease;
        float: right;
    }

    @media (max-width: 960px) {
        .frost-sidebar {
            width: ${({ sidebarVisible }) =>
                sidebarVisible ? sidebarWidth : sidebarCollapsedWidthMobile};
        }
        .frost-content {
            width: ${({ sidebarVisible }) =>
                sidebarVisible ? `calc(100% - ${sidebarWidth})` : "100%"};
        }
    }

    @media (max-width: 768px) {
        .frost-sidebar {
            width: ${({ sidebarVisible }) =>
                sidebarVisible ? sidebarWidth : sidebarCollapsedWidthMobile};
        }
        .frost-content {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .frost-sidebar {
            width: ${({ sidebarVisible }) =>
                sidebarVisible ? sidebarWidth : sidebarCollapsedWidthMobile};
        }
        .frost-content {
            width: 100%;
        }
    }

    @media (max-width: 320px) {
        .frost-sidebar {
            width: ${({ sidebarVisible }) =>
                sidebarVisible ? sidebarWidth : sidebarCollapsedWidthMobile};
        }
        .frost-content {
            width: 100%;
        }
    }
`;

export default FrostStudentLiveTheme;
