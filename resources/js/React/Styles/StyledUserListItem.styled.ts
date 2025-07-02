import React from "react";
import styled from "styled-components";

export const StyledUserListItem = styled.div`
    .student-list {
        width: 100%;
        height: 80px;
        background: #333;
    }
    .profile-photo {
        width: 50px;
    }

    .student-verified {
        background-color: green;
    }

    .student-declined {
        background-color: red;
    }
`;
