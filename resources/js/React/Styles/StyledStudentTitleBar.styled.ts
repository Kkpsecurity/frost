import styled from "styled-components";
import { colors } from "../Config/colors";

const colorPalette = (darkMode) => (darkMode ? colors.dark : colors.light);

export const Navbar = styled.nav`
    padding: 0.5rem 1rem;
    background: ${(props) =>
        `linear-gradient(0deg, ${
            colorPalette(props.darkMode).navbarBgColor
        } 0%, ${colorPalette(props.darkMode).navbarBgColor2} 100%)`};
    box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.75);

    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) {
        padding: 0.5rem 0.75rem;
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) {
        padding: 0.5rem 0.5rem;
    }

    /* Extra small devices (phones, less than 576px) */
    @media (max-width: 575.98px) {
        padding: 0.5rem 0.25rem;
    }
`;

export const Title = styled.h4`
    margin-bottom: 0;
    float: start;
    font-size: 1.2rem;
    font-weight: bold;
    text-transform: uppercase;
    color: ${(props) => colorPalette(props.darkMode).navbarTextColor};

    @media (max-width: 768px) {
        font-size: 1rem;
    }

    @media (max-width: 576px) {
        font-size: 0.8rem;
    }

    @media (max-width: 360px) {
        text-align: center;
        font-size: 1rem;
    }
`;

export const Button = styled.button`
    margin: 1rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    background-color: transparent;
    border: 1px solid ${(props) => colorPalette(props.darkMode).navbarTextColor};
    color: ${(props) => colorPalette(props.darkMode).navbarTextColor};
    transition: all 0.3s ease;    
`;
