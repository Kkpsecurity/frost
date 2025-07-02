import React from "react";
import { Container, Row, Col } from "react-bootstrap";
import { useForm, FormProvider } from "react-hook-form";
import styled from "styled-components";
import { ChatUser } from "../../../../Config/types";

interface InstructorToolsProps {
    chatUser: ChatUser;
    chatEnabled: boolean;
    handleChatToggle: (event: React.ChangeEvent<HTMLInputElement>) => void;
    isInstructor: boolean;
}

const SwitchContainer = styled.div`
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
`;

const SwitchInput = styled.input`
    opacity: 0;
    width: 0;
    height: 0;
`;

const SwitchLabel = styled.label`
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: ${(props) => (props.checked ? "#2196f3" : "#ccc")};
    -webkit-transition: 0.4s;
    transition: 0.4s;
    border-radius: 34px;

    &::before {
        content: "";
        position: absolute;
        left: 0px;
        height: 26px;
        width: 26px;
        background-color: white;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        border-radius: 50%;
        transform: ${(props) => (props.checked ? "translateX(26px)" : "none")};
    }
`;

const SwitchLabelText = styled.span`
    position: absolute;
    width: 50%;
    height: 100%;
    text-align: center;
    line-height: 34px;
    display: none;
    transition: color 0.3s;
    color: ${(props) => (props.checked ? "#fff" : "#000")};
`;

const InstructorTools: React.FC<InstructorToolsProps> = ({
    chatUser,
    chatEnabled,
    handleChatToggle,
    isInstructor,
}) => {
    const methods = useForm();

    return (
        <Container>
            <Row>
                <Col
                    lg={6}
                    style={{
                        display: "flex",
                        justifyContent: "start",
                        alignItems: "center",
                    }}
                >
                    <b>Frost ChatRoom</b>
                </Col>
                <Col lg={6}>
                    <Row>
                        <Col lg={12}>
                            {!isInstructor && (
                                <span
                                    style={{
                                        textAlign: "center",
                                        padding: "5px",
                                        backgroundColor: isInstructor
                                            ? ""
                                            : chatEnabled
                                            ? "#fff"
                                            : "#eee",
                                    }}
                                >
                                    {chatEnabled ? (
                                        <span style={{ color: "green" }}>
                                            <i className="fa fa-wifi"></i> Chat
                                            Enabled
                                        </span>
                                    ) : (
                                        <span style={{ color: "red" }}>
                                            Chat Disabled
                                        </span>
                                    )}
                                </span>
                            )}
                            {isInstructor && (
                                <span
                                    style={{
                                        float: "right",
                                        textAlign: "right",
                                    }}
                                >
                                    <FormProvider {...methods}>
                                        <form
                                            style={{
                                                float: "right",
                                                textAlign: "right",
                                            }}
                                        >
                                            <SwitchContainer>
                                                <SwitchInput
                                                    type="checkbox"
                                                    id="chatEnabled"
                                                    checked={chatEnabled}
                                                    onChange={handleChatToggle}
                                                />
                                                <SwitchLabel
                                                    checked={chatEnabled}
                                                    htmlFor="chatEnabled"
                                                >
                                                    <SwitchLabelText
                                                        checked={chatEnabled}
                                                    >
                                                        {chatEnabled
                                                            ? "ON"
                                                            : "OFF"}
                                                    </SwitchLabelText>
                                                </SwitchLabel>
                                            </SwitchContainer>
                                        </form>
                                    </FormProvider>
                                </span>
                            )}
                        </Col>
                    </Row>
                </Col>
            </Row>
        </Container>
    );
};

export default InstructorTools;

// <Col lg={6}>
//                     {isInstructor && (
//                         <span
//                             style={{
//                                 float: "right",
//                                 textAlign: "right",
//                             }}
//                         >
//                             <FormProvider {...methods}>
//                                 <form
//                                     style={{
//                                         float: "right",
//                                         textAlign: "right",
//                                     }}
//                                 >
//                                     <SwitchContainer>
//                                         <SwitchInput
//                                             type="checkbox"
//                                             id="chatEnabled"
//                                             checked={chatEnabled}
//                                             onChange={handleChatToggle}
//                                         />
//                                         <SwitchLabel
//                                             checked={chatEnabled}
//                                             htmlFor="chatEnabled"
//                                         >
//                                             <SwitchLabelText
//                                                 checked={chatEnabled}
//                                             >
//                                                 {chatEnabled ? "ON" : "OFF"}
//                                             </SwitchLabelText>
//                                         </SwitchLabel>
//                                     </SwitchContainer>
//                                 </form>
//                             </FormProvider>
//                         </span>
//                     )}

//                     {!isInstructor && (
//                         <span
//                             style={{
//                                 float: "right",
//                                 textAlign: "right",
//                                 padding: "5px",
//                             }}
//                         >
//                             {chatEnabled ? (
//                                 <span style={{ color: "green" }}>
//                                     <i className="fa fa-wifi"></i> Chat Enabled
//                                 </span>
//                             ) : (
//                                 <span style={{ color: "red" }}>
//                                     Chat Disabled
//                                 </span>
//                             )}
//                         </span>
//                     )}
//                 </Col>
