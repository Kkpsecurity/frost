import React, { useState, useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";
import { ChatUser } from "../../../Config/types";
import { ChatBox } from "./ChatBox";
import InstructorTools from "./InstructorTools";
import TextInput from "../../../Shared/Components/FormFields/TextInput";

import {
    enableFrostChat,
    postFrostMessage,
    getFrostMessages,
} from "../../../Hooks/FrostChatHooks";
import styled from "styled-components";
import { Button, Col, Row } from "react-bootstrap";

// Frost theme color configuration
const colors = {
    dark: {
        navbarBgColor: "var(--frost-primary-color, #212a3e)",
        navbarTextColor: "var(--frost-white-color, #ffffff)",
        mainBgColor: "var(--frost-secondary-color, #394867)",
        chatBgColor: "var(--frost-dark-color, #343a40)",
        textColor: "var(--frost-white-color, #ffffff)",
        accentColor: "var(--frost-highlight-color, #fede59)"
    },
    light: {
        navbarBgColor: "var(--frost-light-color, #f8f9fa)",
        navbarTextColor: "var(--frost-black-color, #000000)",
        mainBgColor: "var(--frost-white-color, #ffffff)",
        chatBgColor: "var(--frost-light-gray-color, #d3d3d3)",
        textColor: "var(--frost-black-color, #000000)",
        accentColor: "var(--frost-primary-color, #212a3e)"
    }
};

interface FrostChatCardProps {
    course_date_id: number;
    isChatEnabled: boolean;
    chatUser: ChatUser;
    darkMode?: boolean;
    debug?: boolean;
}

const StyledCard = styled.div`
    background-color: ${(props) => props.backgroundColor};
    color: ${(props) => props.color};
    width: 100%;
    overflow: hidden;
`;

const StyledHeader = styled.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px;
    height: 50px;
    background-color: ${(props) => props.backgroundColor};
    text-align: right;
`;

const StyledBody = styled.div`
    height: ${(props) => props.height};
    display: ${(props) => props.display};
    background-color: ${(props) => props.backgroundColor};


`;

const StyledFooter = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;
    background-color: ${(props) => props.backgroundColor};
    height: 50px;
    text-align: left;
    width: 100%;
    overflow: hidden;

    div {
        flex-grow: 1; // This will allow the div to take up available space
        padding: 0;
        margin: 0;

        input {
            width: 100%;
        }
    }
`;

const StyledChatInput = styled(TextInput)`
    .form-control {
        background-color: var(--frost-dark-color, #343a40) !important;
        border: 1px solid var(--frost-base-color, #9ba4b5) !important;
        color: var(--frost-white-color, #ffffff) !important;
        font-size: 0.9rem;
        padding: 10px 12px;
        border-radius: 6px;
        
        &:focus {
            background-color: var(--frost-secondary-color, #394867) !important;
            border-color: var(--frost-highlight-color, #fede59) !important;
            box-shadow: 0 0 0 0.2rem rgba(254, 222, 89, 0.25) !important;
            color: var(--frost-white-color, #ffffff) !important;
        }

        &::placeholder {
            color: var(--frost-base-color, #9ba4b5) !important;
            opacity: 0.8;
        }
    }
`;

const StyledSendButton = styled(Button)`
    background-color: var(--frost-highlight-color, #fede59);
    border: 1px solid var(--frost-warning-color, #d4a71f);
    color: var(--frost-black-color, #000000);
    font-weight: 600;
    padding: 8px 16px;
    margin-left: 8px;
    transition: all 0.3s ease;

    &:hover {
        background-color: var(--frost-warning-color, #d4a71f);
        border-color: var(--frost-highlight-color, #fede59);
        color: var(--frost-black-color, #000000);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(254, 222, 89, 0.3);
    }

    &:focus {
        background-color: var(--frost-warning-color, #d4a71f);
        border-color: var(--frost-highlight-color, #fede59);
        color: var(--frost-black-color, #000000);
        box-shadow: 0 0 0 0.2rem rgba(254, 222, 89, 0.5);
    }
`;

const FrostChatCard: React.FC<FrostChatCardProps> = ({
    course_date_id,
    isChatEnabled,
    chatUser,
    darkMode = true,
    debug = false,
}) => {
    const [chatEnabled, setChatEnabled] = useState(isChatEnabled);
    const isInstructor = chatUser.user_type === "instructor";
    const colorSet = darkMode === true ? colors.dark : colors.light;
    const handleChatToggle = (event: React.ChangeEvent<HTMLInputElement>) => {
        setChatEnabled((prevChatEnabled) => !prevChatEnabled);
        enableFrostChat(String(course_date_id));
    };

    const methods = useForm();
    const { mutateAsync: postMessage } = postFrostMessage(
        String(course_date_id),
        String(chatUser.id)
    );

    const { chatMessages, isLoading, isError, error } = getFrostMessages(
        String(course_date_id),
        String(chatUser.id)
    );

    // console.log("chatMessages", chatMessages);

    const onSubmitMessage = React.useCallback(
        (data: { body: string }) => {
            postMessage({
                course_date_id: String(course_date_id),
                user_id: chatUser.id,
                message: data.body,
                user_type: chatUser.user_type,
            });
            methods.reset();
        },
        [course_date_id, chatUser, postMessage, methods]
    );

    useEffect(() => {
        setChatEnabled(isChatEnabled);
    }, [isChatEnabled]);

    if (isLoading) {
        return <p>Loading chat messages...</p>;
    }

    if (isError) {
        console.error("Chat error:", error);
        return (
            <div>
                <p>Chat temporarily unavailable</p>
                {debug && <pre>{JSON.stringify(error, null, 2)}</pre>}
            </div>
        );
    }

    return (
        <StyledCard
            backgroundColor={colorSet.navbarBgColor}
            color={colorSet.navbarTextColor}
        >
            <StyledHeader backgroundColor={colorSet.navbarBgColor}>
                <InstructorTools
                    chatUser={chatUser}
                    chatEnabled={chatEnabled}
                    handleChatToggle={handleChatToggle}
                    isInstructor={isInstructor}
                />
            </StyledHeader>
            {isChatEnabled ? (
                <FormProvider {...methods}>
                    <form
                        onSubmit={methods.handleSubmit(onSubmitMessage)}
                        style={{
                            marginBottom: "10px",
                        }}
                    >
                        {chatEnabled && (
                            <StyledBody
                                height="400px"
                                display={chatEnabled ? "block" : "none"}
                                backgroundColor={colorSet.chatBgColor}
                            >
                                <ChatBox
                                    chatMessages={chatMessages}
                                    chatEnabled={chatEnabled}
                                    colorSet={colorSet}
                                />
                            </StyledBody>
                        )}
                        <StyledFooter backgroundColor={colorSet.mainBgColor}>
                            {chatEnabled && (
                                <Row>
                                    <Col
                                        lg={12}
                                        style={{
                                            display: "flex",
                                            alignItems: "center",
                                            padding: "5px 0",
                                        }}
                                    >
                                        <div>
                                            <StyledChatInput
                                                id="body"
                                                value=""
                                                title={false}
                                                required={true}
                                            />
                                        </div>
                                        <StyledSendButton type="submit">
                                            <i className="fa fa-paper-plane" style={{marginRight: '6px'}}></i>
                                            Send
                                        </StyledSendButton>
                                    </Col>
                                </Row>
                            )}
                        </StyledFooter>
                    </form>
                </FormProvider>
            ) : null}
        </StyledCard>
    );
};

export default FrostChatCard;
