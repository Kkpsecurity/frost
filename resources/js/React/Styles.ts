import styled from "styled-components";

export const PhotoTitle = styled.h3`
    margin: 0 0 1rem;
    font-size: 1.25rem;
    color: #ffffff;
    font-weight: 600;
`;

export const StyledButton = styled.button`
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    border: 1px solid rgba(0, 0, 0, 0.2);
    background: #3498db;
    color: #ffffff;
    font-weight: 500;

    &:hover {
        background: #2980b9;
        color: #ffffff;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: #6c757d;
        color: #ffffff;
    }
`;

export const Icon = styled.i`
    font-size: 1.2rem;
    margin-right: 0.5rem;
`;

export const StyledAlert = styled.div`
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0.375rem;
    background: #e8f4f8;
    border: 1px solid #3498db;
    color: #2c3e50;
    font-weight: 500;
`;

export const StyledContainer = styled.div`
    padding: 1rem;
    background: #34495e;
    border-radius: 0.5rem;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
`;

export const StyledCaputureDevices = styled.div`
    background: #34495e;
    border-radius: 0.5rem;
    padding: 1rem;
`;

export const StyledCardHeader = styled.div`
    background: #2c3e50;
    color: white;
    padding: 1rem;
    border-radius: 0.5rem 0.5rem 0 0;
    font-weight: bold;
`;

export const StyledRow = styled.div`
    display: flex;
    flex-wrap: wrap;
    margin: 0 -0.5rem;
`;

export const StyledCol = styled.div`
    flex: 1;
    padding: 0 0.5rem;
`;

export const StyledDeviceTitle = styled.h4`
    color: #ffffff;
    margin: 1rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
`;

export const StyledButtonGroup = styled.div`
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin: 1rem 0;
`;

export const StyledCard = styled.div`
    background: #fff;
    border-radius: 0.5rem;
    border: 1px solid #ddd;
    overflow: hidden;
`;

export const StyledImage = styled.img`
    width: 100%;
    height: auto;
    display: block;
`;

export const StyledDropArea = styled.div`
    border: 2px dashed #ccc;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    border-radius: 0.5rem;

    &:hover {
        border-color: #3498db;
        background: #f8f9fa;
    }
`;
