import React, { useRef, useEffect } from "react";
import { Alert } from "react-bootstrap";
import styled from "styled-components";
import { ChatUser, ReturnChatMessageType } from "../../../Config/types";

interface ChatBoxProps {
    chatMessages: ReturnChatMessageType[];
    chatEnabled: boolean;
    colorSet: {
        navbarBgColor: string;
        navbarTextColor: string;
        mainBgColor: string;
        chatBgColor: string;
        textColor: string;
        accentColor: string;
    };
}

const ChatMessageContainer = styled.div`
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto; /* Enables vertical scrolling */
    padding-left: 0;
    list-style-type: none;
`;

const ChatBoxMessage = styled.div`
    background: ${(props) => props.backgroundColor || 'var(--frost-secondary-color, #394867)'};
    margin: 5px auto;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: flex-start;
    color: ${(props) => props.textColor || 'var(--frost-white-color, #ffffff)'};
    border: 1px solid var(--frost-base-color, #9ba4b5);
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
    color: ${(props) => props.textColor || 'var(--frost-white-color, #ffffff)'};
`;

const ChatBoxPostedDate = styled.div`
    color: ${(props) => props.textColor || 'var(--frost-base-color, #9ba4b5)'};
    font-size: 0.85rem;
    margin-bottom: 5px;
`;

const ChatMessage = styled.div`
    padding: 8px 12px;
    background: ${(props) => (props.isInstructor ?
        'var(--frost-highlight-color, #fede59)' :
        'var(--frost-accent-color-2, #6dabca)')};
    border-radius: 12px;
    color: ${(props) => (props.isInstructor ?
        'var(--frost-black-color, #000000)' :
        'var(--frost-white-color, #ffffff)')};
    max-width: 95%;
    min-width: 50%;
    display: inline-block;
    font-size: 1rem;
    margin: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    border: 1px solid ${(props) => (props.isInstructor ?
        'var(--frost-warning-color, #d4a71f)' :
        'var(--frost-info-color, #17aac9)')};
`;

export const ChatBox = ({ chatMessages, chatEnabled, colorSet }: ChatBoxProps) => {
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
                                    <ChatBoxMessage
                                        backgroundColor={colorSet.mainBgColor}
                                        textColor={colorSet.textColor}
                                    >
                                        {showAvatar && (
                                            <ChatBoxAvatar
                                                src={message.user.user_avatar}
                                            />
                                        )}
                                        <ChatBoxHeader>
                                            <ChatBoxUserName textColor={colorSet.textColor}>
                                                {message.user.user_name}
                                            </ChatBoxUserName>
                                            <ChatBoxPostedDate textColor={colorSet.textColor}>
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
                    <div style={{
                        padding: '20px',
                        textAlign: 'center',
                        color: colorSet.textColor,
                        backgroundColor: colorSet.mainBgColor,
                        border: `1px solid ${colorSet.accentColor}`,
                        borderRadius: '8px',
                        margin: '10px'
                    }}>
                        <i className="fa fa-comments" style={{marginRight: '8px'}}></i>
                        No messages yet. Say Hi!
                    </div>
                )
            ) : (
                <div style={{
                    padding: '20px',
                    textAlign: 'center',
                    color: colorSet.textColor,
                    backgroundColor: colorSet.mainBgColor,
                    border: `2px solid var(--frost-danger-color, #f44336)`,
                    borderRadius: '8px',
                    margin: '10px'
                }}>
                    <h5 style={{color: 'var(--frost-danger-color, #f44336)', marginBottom: '10px'}}>
                        <i className="fa fa-ban" style={{marginRight: '8px'}}></i>
                        ChatRoom is Disabled
                    </h5>
                    <p style={{margin: 0, color: colorSet.textColor}}>
                        Enable chat to start messaging with students.
                    </p>
                </div>
            )}
        </>
    );
};
