import styled from "styled-components";
import { colors } from "../Config/colors";

export const PVContainer = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    background-color: #343a40;

    @media (max-width: 768px) {
        padding: 10px;
    }
`;

export const StyledCard = styled.div`
    width: 100%;
    max-width: 1080px;
    background: #b8b8b8;
    border-radius: 8px;
    box-shadow: 0 6px 10px 0 rgba(0, 0, 0, 0.2);
    overflow: hidden;

    @media (max-width: 768px) {
        border-radius: 0;
    }
`;

export const CardHeader = styled.div`
    position: relative; 
    padding: 20px;
    background-color: #131736;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;

    h3 {
        margin: 0;
        font-size: 24px;
        color: #fff;
    }

    b {
        font-weight: 600;
    }

    @media (max-width: 768px) {
        padding: 10px;

        h3 {
            font-size: 20px;
        }
    }
`;

export const CardBody = styled.div`
    padding: 20px;
    background-color: #fff;

    @media (max-width: 768px) {
        padding: 10px;
    }
`;

export const CaptureContainer = styled.div`
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    border: 1px solid #ccc;

    @media (max-width: 768px) {
        padding: 10px;
    }
`;
