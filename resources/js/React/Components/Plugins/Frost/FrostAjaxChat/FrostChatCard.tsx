import React, { useState, useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";
import { ChatUser } from "../../../../Config/types";
import { colors } from "../../../../Config/colors";
import { ChatBox } from "./ChatBox";
import InstructorTools from "./IntructorTools";
import CheckBox from "../../../../Components/FormElements/CheckBox";
import TextInput from "../../../../Components/FormElements/TextInput";
import debounce from "lodash/debounce";

import {
    enableFrostChat,
    postFrostMessage,
    getFrostMessages,
} from "../../../../Hooks/FrostChatHooks";
import styled from "styled-components";
import { Button, Col, Row } from "react-bootstrap";

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

// Continue with other styled components...

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
        String(chatUser.user_id)
    );

    const { chatMessages, isLoading, isError, error } = getFrostMessages(
        String(course_date_id),
        String(chatUser.user_id)
    );

    // console.log("chatMessages", chatMessages);

    const onSubmitMessage = React.useCallback(
        (data: { body: string }) => {
            postMessage({
                course_date_id: String(course_date_id),
                user_id: chatUser.user_id,
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
        return <p>Loading...</p>;
    }

    if (isError) {
        return <p>Error</p>;
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
                                backgroundColor="#f5f5f5"
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
                                            <TextInput
                                                id="body"
                                                value=""
                                                title={false}
                                                required={true}                                            
                                            />
                                        </div>
                                        <Button variant="primary" type="submit">
                                            Send
                                        </Button>
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
