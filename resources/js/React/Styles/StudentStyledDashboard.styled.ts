import React from "react";
import styled from "styled-components";

export const StudentStyledDashboard = styled.div`
    display: flex;
    flex-direction: column;
    margin: 0 auto;
    padding: 0 0 0 0;
    width: 100%;
    height 100%;

    .student-navbar {
        position: relative;
    }

    /* Responsive Styles */
    @media (max-width: 576px) {
        margin: 0;
        padding: 0;

        .student-navbar {
            position: relative;
        }
    }
    @media (max-width: 480px) {
        .student-navbar {
            margin-top: -20px;
        }
    }
`;
