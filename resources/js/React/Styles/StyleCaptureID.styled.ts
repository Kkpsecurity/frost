import styled from "styled-components";

export const StyledContainer = styled.div`
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
    overflow: hidden;

    @media (max-width: 768px) {
        max-width: 100%;
        width: 100%;
        min-width: 0;
        height: 100%;
        border-radius: 0;
        padding: 0;
    }

    @media (max-width: 576px) {
        max-width: 100%;
        width: 100%;
        min-width: 0;
        height: 100%;
        border-radius: 0;
        padding: 0;

        div.alert {
            border-radius: 0;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        div.alert > p {
            padding: 0.5rem;
            margin: 0;
        }
    }

    @media (max-width: 320px) {
        max-width: 100%;
        width: 100%;
        min-width: 0;
        height: 100%;
        border-radius: 0;
        padding: 0;
    }
`;

export const StyledDeviceRow = styled.div`
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    margin-right: 0;
    margin-left: 0;
`;


export const StyledCol = styled.div`
    flex: 0 0 auto;
    max-width: 50%;
    flex-basis: 50%;
    padding-right: 15px;
    padding-left: 15px;
    height: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #ccc;
`;


export const Icon = styled.i`
    margin-right: 1rem;
    font-size: 2em;
    color: #dc3545;
    float: left;

    @media (max-width: 768px) {
        font-size: 1.5em;
    }

    @media (max-width: 576px) {
        font-size: 1.5em;
    }

    @media (max-width: 320px) {
        font-size: 1.3em;
    }
`;
