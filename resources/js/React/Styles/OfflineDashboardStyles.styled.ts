import { Container, Col, Row, Card, Alert, ListGroup } from "react-bootstrap";
import styled from "styled-components";
import { colors } from "../Config/colors";

const colorPalette = (darkMode) => (darkMode ? colors.dark : colors.light);

const LgContainer = styled(Container)`
    display: block;
    min-height: 100vh;
    height: 100%;
    width: 100%;
    padding: 20px 15px 20px 15px;
`;

const ResponsiveCol = styled(Col)``;

const StyledRow = styled(Row)`
    margin-top: 15px;
`;

interface StyledProps {
    darkMode: Boolean;
}

const StyledCard = styled(Card)<StyledProps>`
    background-color: ${({ darkMode }) => colorPalette(darkMode).mainBgColor};
    color: ${({ darkMode }) => colorPalette(darkMode).navbarTextColor};
`;

const StyledCardHeader = styled(Card.Header)<StyledProps>`
    font-size: 1.3em;
    font-weight: bold;
    color: ${({ darkMode }) => colorPalette(darkMode).highlightColor};
    background-color: ${({ darkMode }) =>
        colorPalette(darkMode).navbarBgColor2};
`;

const StyledListGroup = styled(ListGroup)<StyledProps>`
    background-color: ${({ darkMode }) =>
        colorPalette(darkMode).contentBgColor};
    color: ${({ darkMode }) => colorPalette(darkMode).navbarTextColor};
`;

const StyledListGroupItem = styled(ListGroup.Item)<StyledProps>`
    background-color: ${({ darkMode }) =>
        colorPalette(darkMode).contentBgColor};
    color: ${({ darkMode }) => colorPalette(darkMode).navbarTextColor};
`;

const StyledAlert = styled(Alert)`
    margin-top: 15px;
    background-color: #f8d7da;
`;

const StyledEmbedContainer = styled.div`
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 30px;
    height: 0;
    overflow: hidden;
`;

const StyledSubTitle = styled.div`
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
`;

const StyledLead = styled.p`
    font-size: 1.5em;
    font-weight: bold;
    color: ${({ darkMode }) => colorPalette(darkMode).highlightColor};
`;

export {
    LgContainer,
    ResponsiveCol,
    StyledRow,
    StyledCard,
    StyledCardHeader,
    StyledListGroup,
    StyledListGroupItem,
    StyledAlert,
    StyledEmbedContainer,
    StyledSubTitle,
    StyledLead,
};