import styled from 'styled-components';
import { Navbar, Col } from 'react-bootstrap';

const StyledNavbar = styled(Navbar)`
    &.student-navbar {
        background-color: black;  // Replaced with a standard CSS color
        color: white;             // Replaced with a standard CSS color
        padding: 10px 20px;
        border-radius: 0;
       
    }
`;

const StyledCol = styled(Col)`
    display: flex;
    align-items: center;
`;

const StyledColStart = styled(StyledCol)`
    justify-content: flex-start;
`;

const StyledColEnd = styled(StyledCol)`
    justify-content: flex-end;
`;

const ExamButton = styled.a`
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: green;  // Replaced with a standard CSS color
    color: white;             // Replaced with a standard CSS color
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 10px;

    &:hover {
        background-color: darkgreen;  // Replaced with a standard CSS color
    }
`;

const DarkModeButton = styled.button`
    background-color: black;  // Replaced with a standard CSS color
    color: white;             // Replaced with a standard CSS color
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;

    &:hover {
        background-color: gray;  // Replaced with a standard CSS color
    }
`;

const HelpButton = styled.button`
    background-color: black;  // Replaced with a standard CSS color
    color: white;             // Replaced with a standard CSS color
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;

    &:hover {
        background-color: gray;  // Replaced with a standard CSS color
    }
`;

export { StyledNavbar, StyledColStart, StyledColEnd, ExamButton, DarkModeButton, HelpButton };
