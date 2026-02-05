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
    aiMonitoringEnabled?: boolean;
    handleAiToggle?: (event: React.ChangeEvent<HTMLInputElement>) => void;
    handlePresetsClick?: () => void;
}

const SwitchContainer = styled.div`
    position: relative;
    display: inline-block;
    width: 46px;
    height: 26px;
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
    border-radius: 26px;

    &::before {
        content: "";
        position: absolute;
        left: 2px;
        top: 2px;
        height: 22px;
        width: 22px;
        background-color: white;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        border-radius: 50%;
        transform: ${(props) => (props.checked ? "translateX(20px)" : "none")};
    }
`;

const SwitchLabelText = styled.span`
    position: absolute;
    width: 50%;
    height: 100%;
    text-align: center;
    line-height: 26px;
    display: none;
    transition: color 0.3s;
    color: ${(props) => (props.checked ? "#fff" : "#000")};
`;

const AiButton = styled.button<{ enabled: boolean }>`
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid ${(props) => (props.enabled ? "#17a2b8" : "#6c757d")};
    background-color: ${(props) => (props.enabled ? "#17a2b8" : "#6c757d")};
    color: white;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-left: 12px;

    &:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    &:active {
        transform: translateY(0);
    }

    i {
        font-size: 0.9rem;
    }
`;

const PresetsButton = styled.button`
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid #28a745;
    background-color: #28a745;
    color: white;
    cursor: pointer;
    handlePresetsClick,
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-left: 12px;

    &:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    &:active {
        transform: translateY(0);
    }

    i {
        font-size: 0.9rem;
    }
`;

const InstructorTools: React.FC<InstructorToolsProps> = ({
    chatUser,
    chatEnabled,
    handleChatToggle,
    isInstructor,
    aiMonitoringEnabled = false,
    handleAiToggle,
    handlePresetsClick,
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
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "flex-end",
                                    }}
                                >
                                    <FormProvider {...methods}>
                                        <form
                                            style={{
                                                display: "flex",
                                                alignItems: "center",
                                                gap: "0px",
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
                                            {/* AI Toggle - Hidden for now, moving to Sentinel system
                                            {handleAiToggle && (
                                                <AiButton
                                                    type="button"
                                                    enabled={
                                                        aiMonitoringEnabled
                                                    }
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        handleAiToggle({
                                                            target: {
                                                                checked:
                                                                    !aiMonitoringEnabled,
                                                            },
                                                        } as React.ChangeEvent<HTMLInputElement>);
                                                    }}
                                                >
                                                    <i className="fas fa-robot"></i>
                                                    AI{" "}
                                                    {aiMonitoringEnabled
                                                        ? "On"
                                                        : "Off"}
                                                </AiButton>
                                            )}
                                            */}
                                            {handlePresetsClick && (
                                                <PresetsButton
                                                    type="button"
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        handlePresetsClick();
                                                    }}
                                                >
                                                    <i className="fas fa-list"></i>
                                                    Presets
                                                </PresetsButton>
                                            )}
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
