import React from "react";
import styled from "styled-components";
import { Row, Col, Toast, Button, Container } from "react-bootstrap";
import ImageBox from "../Pages/Admin/Instructors/FrostTraining/Partials/ImageBox";

export const StyledContainer = styled(Container)`
    margin-top: 2rem; // Some space between the container and the elements above
`;

export const StyledRow = styled(Row)`
    display: block; // Block display for the row
    padding: 1rem;
    background-color: #f5f5f5; // Light gray background for the row
    border-radius: 8px; // Rounded corners for the row
    margin-bottom: 1rem; // Space between the row and other elements below
`;

export const StyledCol = styled(Col)`
    border: 1px solid #e0e0e0; // A subtle border for the columns
    padding: 1rem; // Inner padding for content inside the column
    transition: transform 0.3s; // Smooth transition for hover effect

    &:hover {
        transform: scale(1.05); // A slight zoom effect on hover
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); // A shadow for elevated look on hover
    }
`;

export const ValidationStatus = styled.div<{ status: boolean | null }>`
    color: ${({ status }) =>
        status === null
            ? "#333"
            : status
            ? "green"
            : "red"}; // Color based on the validation status
    margin-top: 0.5rem; // Some space between the status and the button
    font-weight: bold; // Bold text for emphasis
    font-size: 1.2rem; // Slightly larger font size for the status
    text-transform: capitalize; // Capitalizing the status text
    display: flex; // Flex display for alignment
    justify-content: center; // Centering the text horizontally
    align-items: center; // Centering the text vertically    
`;

export const StyledToast = styled(Toast)`
    font-size: 0.9rem; // Adjusting font size for toast
    background-color: #333; // Dark background for the toast
    color: #fff; // White text for contrast
    border: none; // Removing any default border
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); // Shadow for the toast
    max-width: 300px; // Maximum width of the toast
    margin: 1rem auto; // Centering the toast with some margin
`;
