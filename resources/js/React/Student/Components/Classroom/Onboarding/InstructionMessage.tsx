import React from "react";
import styled from "styled-components";

const MessageContainer = styled.div<{ type?: string }>`
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    background-color: ${props => props.type === 'error' ? '#f8d7da' : props.type === 'success' ? '#d4edda' : '#d1ecf1'};
    border: 1px solid ${props => props.type === 'error' ? '#f5c6cb' : props.type === 'success' ? '#c3e6cb' : '#bee5eb'};
    color: ${props => props.type === 'error' ? '#721c24' : props.type === 'success' ? '#155724' : '#0c5460'};
`;

const MessageText = styled.p`
    margin: 0;
    font-size: 1rem;
    line-height: 1.5;
`;

interface InstructionMessageProps {
    validations: {
        message: string;
        type?: 'info' | 'success' | 'error';
    };
}

const InstructionMessage: React.FC<InstructionMessageProps> = ({ validations }) => {
    if (!validations.message) return null;

    return (
        <MessageContainer type={validations.type || 'info'}>
            <MessageText>{validations.message}</MessageText>
        </MessageContainer>
    );
};

export default InstructionMessage;
