import styled from "styled-components";

export const StyledCaputureDevices = styled.div`
    position: relative;
    width: 100%;
    height: 100%;
`;

export const StyledCardHeader = styled.div`
    background-color: #f5f5f5;
    border-bottom: 1px solid #ddd;
    padding: 0.75rem 1.25rem;
    color: #333;
    font-size: 1.25rem;
    font-weight: bold;
`;

export const StyledRow = styled.div`
    width: 100%;
    height: auto;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    margin-right: -15px;
    margin-left: -15px;
    padding: 10px 5px;
    border-bottom: 1px solid #ddd;
    background-color: #ccc;

    @media (max-width: 768px) {
        margin-right: 0;
        margin-left: 0;
        padding: 10px 0;
    }
`;

export const StyledCol = styled.div`
    display: flex;
    flex: 0 0 ${(props) => props.flexBasis || "100%"};
    flex-direction: column;
    width: 100%;
    height: auto;
    justify-content: center;
    object-fit: contain;
    align-items: center;
    padding-right: 15px;
    padding-left: 15px;

    @media (max-width: 768px) {
        padding-right: 0;
        padding-left: 0;
    }
`;

export const StyledButtonGroup = styled.div`
    text-align: right;    
`;

export const StyledDeviceTitle = styled.div`
    display: flex;
    justify-content: center;
    width: 100%;
    height: 100%;
    align-items: center;
    font-size: 0.8rem;
    font-weight: bold;
    color: #333;
    background-color: #f5f5f5;

    img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
`;
