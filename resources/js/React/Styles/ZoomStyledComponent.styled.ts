import styled from "styled-components";

export const ZoomContainer = styled.div`
    position: relative;
    padding: 0;
    margin: 0;
    width: 100%;
    height: auto;
    box-shadow: 0px 0px 2px ${(props) => props.shadowColor || 'rgba(0, 255, 0, 0.5)'};
`;

export const StyledEmbedContainer = styled.div`
    width: 100%;
    font-size: 1.2rem;
    font-weight: normal;
    font-family: "Roboto", sans-serif;
    color: ${(props) => props.textColor || props.currentColors.navbarTextColor};
    background-color: ${(props) => props.bgColor || props.currentColors.contentBgColor};

    @media (max-width: 992px) and (min-width: 768px) {
        font-size: 1rem; // Optimized for tablets
    }

    @media (max-width: 576px) {
        font-size: 0.9rem;
        align-items: top;
    }
`;

export const StyledVideoTitle = styled.h3`
    display: flex;
    justify-content: start;
    align-items: center;
    margin: 0;
    width: 100%;
    height: 40px;
    padding: 0 10px;
    font-size: 1.4rem;
    font-weight: bold;
    line-height: 1.2;
    font-family: "Arial", sans-serif;
    color: ${(props) => props.textColor || props.currentColors.navbarTextColor};

    flex: 1;

    i {
        margin-right: 15px;
    }

    @media (max-width: 768px) {
        width: 100%;
        padding: 10px 0;
    }

    @media (max-width: 992px) and (min-width: 768px) {
        font-size: 1.3rem; 
        padding: 15px 0; 
    }

    @media (max-width: 576px) {
        font-size: 1rem;
    }
`;

export const StyledSubTitle = styled.div`
    width: 100%;
    margin: 0;
    display: flex;
    justify-content: center;
    background-color: ${(props) => props.bgColor || props.currentColors.contentBgColor};
    color: ${(props) => props.textColor || props.currentColors.navbarTextColor};
    padding: 0;

    flex: 2;

    @media (max-width: 768px) {
        width: 100%;
    }
`;

export const StyledLead = styled.p`
    width: 100%;
    font-size: 1.2rem;
    text-align: center;
    font-weight: normal;
    font-family: "Arial", sans-serif;
    color: ${(props) => props.highlightColor || props.currentColors.hightlightTextColor};
    background-color: ${(props) => props.bgColor || props.currentColors.contentBgColor};
    margin: 0;
    padding: 10px;
    box-shadow: 0px 0px 2px ${(props) => props.shadowColor || 'rgba(0, 255, 0, 0.5)'};

    @media (max-width: 992px) and (min-width: 768px) {
        font-size: 1.1rem;
        padding: 12px;
    }
    @media (max-width: 576px) {
        font-size: 0.8rem;
        padding: 5px;
    }
`;
