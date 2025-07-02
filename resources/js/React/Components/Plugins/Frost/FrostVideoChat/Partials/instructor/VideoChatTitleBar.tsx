import React from "react";
import styled from "styled-components";

// Define your styled-components
const Container = styled.div`
    position: relative;
    bottom: 0;
    display: block;
    height: 55px !important;
    background-color: #444;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #ccc;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.75);
`;

const Title = styled.div`
    display: flex;
    align-items: center;                        
    font-size: 1.5rem;
    font-weight: bold;
    text-align: left;
    color: #fff;
`;

const ButtonContainer = styled.div`
    display: flex;
    align-items: center;
    justify-content: flex-end;
`;

const CallButton = styled.button`
    margin-right: 1rem;
    float: right;
    color: #fff;
    background-color: ${(props) => (props.makeCall ? "#dc3545" : "#28a745")};
    border-color: ${(props) => (props.makeCall ? "#dc3545" : "#28a745")};
`;

const VideoChatTitleBar = ({ makeCall, allQueueStudents, setMakeCall }) => {
    /**
     * The Button Text for the Make Call button
     */
    const buttonText = makeCall === true ? "Close" : "Open";

    return (
        <Container>
            <div className="row">
                <Title className="col-9">
                   Students inQueue: {allQueueStudents.length}
                </Title>
                <ButtonContainer className="col-3">
                    <CallButton
                        makeCall={makeCall}
                        onClick={() => setMakeCall(!makeCall)}
                    >
                        {buttonText}
                    </CallButton>
                </ButtonContainer>
            </div>
        </Container>
    );
};

export default VideoChatTitleBar;
