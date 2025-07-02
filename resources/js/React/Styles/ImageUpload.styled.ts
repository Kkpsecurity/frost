import { Card } from 'react-bootstrap';
import styled from 'styled-components';

const StyledDropArea = styled.p`
    border: 1px dashed #ccc;
    border-radius: 5px;
    width: ${(props) => props.width};
    height: ${(props) => props.height};
    text-align: center;
`;

const StyledCard = styled(Card)`
    padding: 0;
    width: 100%;
    height: auto;
    max-height: 100%;

    @media (max-width: 576px) {
        min-height: 300px;
    }

    @media (max-width: 320px) {
        min-height: 200px;
    }
`;

const StyledImage = styled(Card.Img)`
    display: Flex;
    justify-content: center;
    align-items: center;     
    width: 100%;
    height: auto;
    max-height: 100%;
    
    object-fit: cover;
`;

export { StyledDropArea, StyledCard, StyledImage };
