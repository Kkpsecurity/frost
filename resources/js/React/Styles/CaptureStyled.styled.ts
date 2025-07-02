import styled from 'styled-components';

export const StyledContainer = styled.div`
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;

    @media (max-width: 768px) {
        padding-right: 0;
        padding-left: 0;
    }
`;

export const StyledAlert = styled.div`
    background-color: #fce4d6; // Based on Bootstrap's warning variant
    border: 1px solid #f8d7da;
    padding: 15px;
    border-radius: 3px;
    margin-bottom: 20px;
`;

export const PhotoTitle = styled.h2`
    margin-bottom: 20px;
    texttransform: uppercase;
    font-weight: 700;
    font-size: 1.5rem;
`;

export const StyledButton = styled.button`
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #007bff; // Bootstrap's primary color
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;

    &:hover {
        background-color: #0056b3;
    }

    @media (max-width: 768px) {
        padding: 8px 16px;
    }
`;