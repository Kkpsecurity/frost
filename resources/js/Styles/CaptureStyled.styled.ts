import styled from "styled-components";

export const PhotoTitle = styled.h3`
    margin: 0 0 1rem;
    font-size: 1.25rem;
`;

export const StyledButton = styled.button`
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    border: 1px solid rgba(0, 0, 0, 0.2);
    background: #f8f9fa;
    color: #212529;

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;
