import styled from 'styled-components';

import { colors } from '../Config/colors';

export const EXPAND_WIDTH = "320px";
export const COLLAPSE_WIDTH = "50px";

export const Sidebar = styled.aside`
    .sidebar-header {
        width: ${(props) => (props.sidebarVisible ? COLLAPSE_WIDTH : EXPAND_WIDTH)};
        background-color: ${(props) =>
            props.darkMode
                ? colors.dark.sidebarHeaderBgColor
                : colors.light.sidebarHeaderBgColor};
    }

    @media (max-width: 380px) {
        .sidebar-header {
            width: ${(props) => (props.sidebarVisible ? COLLAPSE_WIDTH : EXPAND_WIDTH)};
        }
    }

    .sidebar-content {
        background-color: ${(props) =>
        props.darkMode
            ? colors.dark.sidebarContentBgColor
            : colors.light.sidebarContentBgColor};
    }  
    
    .toggleSidebarButton {
        border-radius: 0;
        background-color: ${(props) =>
            props.darkMode
                ? colors.dark.toggleButtonBgColor
                : colors.light.toggleButtonBgColor};
    
        color: ${(props) =>
            props.darkMode
                ? colors.light.highlightColor
                : colors.light.highlightColor};
    }
`;