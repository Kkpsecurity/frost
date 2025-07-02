import React from "react";
import styled from "styled-components";

export const StyledLessonGroup = styled.div`
    background: #333;
    display: block;
    margin: 0;
    padding: 0;

    .lesson-group {
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .list-group {
        background-color: #fff; /* set the background color to white */
        border: none; /* remove the border */
        border-radius: 0; /* remove the border radius */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* add a subtle box shadow */
    }

 

    .list-group-item.active:hover {
        background-color: #007bff; /* set the background color to a blue color */
        box-shadow: none; /* remove the box shadow */
        color: #fff; /* set the font color to white */
    }
`;
