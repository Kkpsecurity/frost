import React, { useRef, useEffect } from "react";
import { Alert } from "react-bootstrap";
import styled from "styled-components";
import { ChatUser, ReturnChatMessageType } from "../../../../Config/types";

interface ChatBoxProps {
    chatMessages: ReturnChatMessageType[];
    chatEnabled: boolean;
    colorSet: {
        navbarBgColor: string;
        navbarTextColor: string;
    };
}

const ChatMessageContainer = styled.div`
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto; /* Enables vertical scrolling */
    padding-left: 0;
    list-style-type: none;
`;

const ChatBoxMessage = styled.div`
    background: #eff3f7;
    margin: 5px auto;
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    align-items: flex-start;
    color: #000;
`;

const ChatBoxAvatar = styled.img`
    width: 50px;
    height: 50px;
    margin: 20px 10px;
    border-radius: 50%;
`;

const ChatBoxHeader = styled.div`
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 10px;
    width: 100%;
    margin-bottom: 5px;
`;

const ChatBoxUserName = styled.div`
    font-weight: bold;
    font-size: 1rem;
    color: #000;
`;

const ChatBoxPostedDate = styled.div`
    color: #444;
    font-size: 0.95rem;
    margin-bottom: 5px;
`;

const ChatMessage = styled.div`
    padding: 5px;
    background: ${(props) => (props.isInstructor ? "lightblue" : "lightgreen")};
    border-radius: 15px;
    color: #000;
    max-width: 95%;
    min-width: 50%;
    display: inline-block;
    font-size: 1.2rem;
    margin: 10px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
`;

export const ChatBox = ({ chatMessages, chatEnabled }: ChatBoxProps) => {
    const [showAvatar, setShowAvatar] = React.useState<boolean>(true);
    const lastChatMessageRef = useRef<HTMLAnchorElement>(null);
    const chatMessageContainerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (lastChatMessageRef.current && chatMessageContainerRef.current) {
            const chatContainer = chatMessageContainerRef.current;
            const scrollPosition =
                lastChatMessageRef.current.offsetTop - chatContainer.offsetTop;
            chatContainer.scrollTop = scrollPosition;
        }
    }, [chatMessages]);

    return (
        <>
            {chatEnabled ? (
                Array.isArray(chatMessages) && chatMessages.length > 0 ? (
                    <ChatMessageContainer ref={chatMessageContainerRef}>
                        {chatMessages.map(
                            (message: ReturnChatMessageType, index: number) => (
                                <a
                                    href="#"
                                    key={index}
                                    ref={
                                        index === chatMessages.length - 1
                                            ? lastChatMessageRef
                                            : null
                                    }
                                >
                                    <ChatBoxMessage>
                                        {showAvatar && (
                                            <ChatBoxAvatar
                                                src={message.user.user_avatar}
                                            />
                                        )}
                                        <ChatBoxHeader>
                                            <ChatBoxUserName>
                                                {message.user.user_name}
                                            </ChatBoxUserName>
                                            <ChatBoxPostedDate>
                                                <i className="fa fa-clock"></i>{" "}
                                                Posted: {message.created_at}
                                            </ChatBoxPostedDate>
                                            <ChatMessage
                                                isInstructor={
                                                    message.user.user_type ==
                                                    "instructor"
                                                        ? true
                                                        : false
                                                }
                                            >
                                                {message.body}
                                            </ChatMessage>
                                        </ChatBoxHeader>
                                    </ChatBoxMessage>
                                </a>
                            )
                        )}
                    </ChatMessageContainer>
                ) : (
                    <p className="alert alert-danger">
                        No messages yet. Say Hi!
                    </p>
                )
            ) : (
                <Alert variant="danger">
                    <Alert.Heading>ChatRoom is Disabled</Alert.Heading>
                </Alert>
            )}
        </>
    );
};
