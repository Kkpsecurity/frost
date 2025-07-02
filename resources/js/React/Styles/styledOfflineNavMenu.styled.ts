import styled from 'styled-components';
import { colors } from '../Config/colors';

const colorPalette = (darkMode) => (darkMode ? colors.dark : colors.light);

export const Menu = styled.menu`
    margin: 0;
    display: flex;
    width: 100%;
    padding: 0 2.85em;
    position: relative;
    align-items: center;
    justify-content: left;
    background-color: ${(props) => colorPalette(props.darkMode).sidebarBgColor};

    @media (max-width: 768px) {
        padding: 0 1.875em; /* Reduce size by 25% */
    }

    @media (max-width: 576px) {
        padding-left: 0; /* Remove left padding */
    }
`;

export const MenuItem = styled.button`
    all: unset;
    min-width: 60px;
    width: auto;
    height: 60px;
    z-index: 100;
    display: flex;
    cursor: pointer;
    position: relative;
    padding: 0 0.5em;
    align-items: center;
    will-change: transform;
    justify-content: center;
    transition: transform 0.2s ease-in-out;
    background-color: ${(props) => colorPalette(props.darkMode).navbarBgColor};
    color: #fff;

    @media (max-width: 768px) {
        min-width: 45px; /* Reduce size by 25% */
        height: 45px; /* Reduce size by 25% */
        padding: 0 0.375em; /* Reduce padding by 25% */
    }

    &.active,
    &:hover,
    &:focus {
        background-color: var(--frost-dark-hover-color);
        transform: scale(1.1);
    }
`;

export const MenuBorder = styled.div`
    border-top: 1px solid ${(props) => colorPalette(props.darkMode).navbarBgColor};
    margin-top: 20px;
`;