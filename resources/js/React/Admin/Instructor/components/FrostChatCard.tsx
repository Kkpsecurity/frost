import React, { useState, useEffect } from "react";
import { useQueryClient } from "@tanstack/react-query";
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
import { Button, Col, Row, Modal } from "react-bootstrap";

// Frost theme color configuration
const colors = {
    dark: {
        navbarBgColor: "var(--frost-primary-color, #212a3e)",
        navbarTextColor: "var(--frost-white-color, #ffffff)",
        mainBgColor: "var(--frost-secondary-color, #394867)",
        chatBgColor: "var(--frost-dark-color, #343a40)",
        textColor: "var(--frost-white-color, #ffffff)",
        accentColor: "var(--frost-highlight-color, #fede59)",
    },
    light: {
        navbarBgColor: "var(--frost-light-color, #f8f9fa)",
        navbarTextColor: "var(--frost-black-color, #000000)",
        mainBgColor: "var(--frost-white-color, #ffffff)",
        chatBgColor: "var(--frost-light-gray-color, #d3d3d3)",
        textColor: "var(--frost-black-color, #000000)",
        accentColor: "var(--frost-primary-color, #212a3e)",
    },
};

interface FrostChatCardProps {
    course_date_id: number;
    isChatEnabled?: boolean;
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
    const queryClient = useQueryClient();
    const [chatEnabled, setChatEnabled] = useState(!!isChatEnabled);
    const [aiMonitoringEnabled, setAiMonitoringEnabled] = useState(false);
    const [showPresetsModal, setShowPresetsModal] = useState(false);
    const [presets, setPresets] = useState<string[]>([]);
    const [loadingPresets, setLoadingPresets] = useState(false);
    const [editingIndex, setEditingIndex] = useState<number | null>(null);
    const [editText, setEditText] = useState("");
    const [newPresetText, setNewPresetText] = useState("");
    const [saving, setSaving] = useState(false);
    const isInstructor = chatUser.user_type === "instructor";
    const colorSet = darkMode === true ? colors.dark : colors.light;

    const handleChatToggle = async (
        event: React.ChangeEvent<HTMLInputElement>,
    ) => {
        const newState = !chatEnabled;
        setChatEnabled(newState);

        try {
            const response = await enableFrostChat(
                String(course_date_id),
                newState,
            );

            if (response?.enabled !== undefined) {
                setChatEnabled(!!response.enabled);
            }

            await queryClient.invalidateQueries({
                queryKey: ["chatroom", String(course_date_id)],
            });
        } catch (error) {
            // Revert optimistic update if the request fails
            setChatEnabled(!newState);
            console.error("Failed to toggle chat:", error);
        }
    };

    const handleAiToggle = (event: React.ChangeEvent<HTMLInputElement>) => {
        const newState = event.target.checked;
        setAiMonitoringEnabled(newState);

        // TODO: Call backend API to enable/disable AI monitoring
        console.log("🤖 AI Monitoring toggled:", newState);

        // Example API call (implement later):
        // fetch(`/admin/instructors/classroom/ai-monitoring-toggle`, {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ course_date_id, ai_enabled: newState })
        // });
    };

    const handlePresetsClick = async () => {
        console.log("📋 Presets button clicked");
        setLoadingPresets(true);
        setShowPresetsModal(true);

        try {
            const response = await fetch("/admin/instructors/chat-presets");
            const data = await response.json();
            setPresets(data.presets || []);
        } catch (error) {
            console.error("Failed to load presets:", error);
            setPresets([]);
        } finally {
            setLoadingPresets(false);
        }
    };

    const handlePresetSelect = (presetText: string) => {
        // Insert preset text into chat input (field name is "body")
        methods.setValue("body", presetText);
        setShowPresetsModal(false);
    };
    const handleAddPreset = async () => {
        if (!newPresetText.trim()) return;

        const updatedPresets = [...presets, newPresetText.trim()];
        await savePresets(updatedPresets);
        setNewPresetText("");
    };

    const handleEditPreset = (index: number) => {
        setEditingIndex(index);
        setEditText(presets[index]);
    };

    const handleSaveEdit = async () => {
        if (editingIndex === null || !editText.trim()) return;

        const updatedPresets = [...presets];
        updatedPresets[editingIndex] = editText.trim();
        await savePresets(updatedPresets);
        setEditingIndex(null);
        setEditText("");
    };

    const handleCancelEdit = () => {
        setEditingIndex(null);
        setEditText("");
    };

    const handleDeletePreset = async (index: number) => {
        if (!confirm("Delete this preset?")) return;

        const updatedPresets = presets.filter((_, i) => i !== index);
        await savePresets(updatedPresets);
    };

    const savePresets = async (updatedPresets: string[]) => {
        setSaving(true);
        try {
            const response = await fetch("/admin/instructors/chat-presets", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({ presets: updatedPresets }),
            });

            if (response.ok) {
                setPresets(updatedPresets);
            } else {
                alert("Failed to save presets");
            }
        } catch (error) {
            console.error("Error saving presets:", error);
            alert("Error saving presets");
        } finally {
            setSaving(false);
        }
    };
    const methods = useForm();
    const { mutateAsync: postMessage } = postFrostMessage(
        String(course_date_id),
        String(chatUser.id),
    );

    const {
        chatMessages,
        enabled: enabledFromServer,
        isLoading,
        isError,
        error,
    } = getFrostMessages(String(course_date_id), String(chatUser.id));

    const effectiveEnabled = enabledFromServer ?? isChatEnabled ?? false;

    // console.log("chatMessages", chatMessages);

    const onSubmitMessage = React.useCallback(
        async (data: { body: string }) => {
            if (!effectiveEnabled) {
                setChatEnabled(false);
                return;
            }

            try {
                await postMessage({
                    course_date_id: String(course_date_id),
                    user_id: chatUser.id,
                    message: data.body,
                    user_type: chatUser.user_type,
                });
                methods.reset();
            } catch (e) {
                // Avoid unhandled promise rejections (e.g. 403 when chat is disabled)
                console.error("Failed to post message:", e);
                await queryClient.invalidateQueries({
                    queryKey: ["chatroom", String(course_date_id)],
                });
            }
        },
        [
            effectiveEnabled,
            course_date_id,
            chatUser,
            postMessage,
            methods,
            queryClient,
        ],
    );

    useEffect(() => {
        setChatEnabled(!!effectiveEnabled);
    }, [effectiveEnabled]);

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
                    aiMonitoringEnabled={aiMonitoringEnabled}
                    handleAiToggle={handleAiToggle}
                    handlePresetsClick={handlePresetsClick}
                />
            </StyledHeader>
            {effectiveEnabled ? (
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
                                            <i
                                                className="fa fa-paper-plane"
                                                style={{ marginRight: "6px" }}
                                            ></i>
                                            Send
                                        </StyledSendButton>
                                    </Col>
                                </Row>
                            )}
                        </StyledFooter>
                    </form>
                </FormProvider>
            ) : (
                <div style={{ padding: "16px" }}>
                    <p style={{ margin: 0 }}>Chat is disabled.</p>
                </div>
            )}
            {/* Presets Modal */}
            <Modal
                show={showPresetsModal}
                onHide={() => setShowPresetsModal(false)}
                centered
            >
                <Modal.Header
                    closeButton
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                        color: colorSet.navbarTextColor,
                        borderBottom: `1px solid ${colorSet.accentColor}`,
                    }}
                >
                    <Modal.Title>📋 Chat Presets</Modal.Title>
                </Modal.Header>
                <Modal.Body
                    style={{
                        backgroundColor: colorSet.mainBgColor,
                        color: colorSet.textColor,
                    }}
                >
                    {loadingPresets ? (
                        <div className="text-center p-4">
                            <i className="fas fa-spinner fa-spin fa-2x"></i>
                            <p className="mt-2">Loading presets...</p>
                        </div>
                    ) : presets.length === 0 ? (
                        <div className="text-center p-4">
                            <i
                                className="fas fa-inbox fa-2x mb-3"
                                style={{ color: colorSet.accentColor }}
                            ></i>
                            <p>No presets yet.</p>
                            <p className="text-muted small">
                                Add your first preset below!
                            </p>
                        </div>
                    ) : (
                        <div
                            className="list-group"
                            style={{ maxHeight: "400px", overflowY: "auto" }}
                        >
                            {presets.map((preset, index) => (
                                <div
                                    key={index}
                                    className="list-group-item"
                                    style={{
                                        backgroundColor: colorSet.chatBgColor,
                                        color: colorSet.textColor,
                                        border: `1px solid ${colorSet.accentColor}`,
                                        marginBottom: "8px",
                                        borderRadius: "4px",
                                        padding: "12px",
                                    }}
                                >
                                    {editingIndex === index ? (
                                        <div>
                                            <input
                                                type="text"
                                                className="form-control mb-2"
                                                value={editText}
                                                onChange={(e) =>
                                                    setEditText(e.target.value)
                                                }
                                                maxLength={1000}
                                                style={{
                                                    backgroundColor:
                                                        colorSet.mainBgColor,
                                                    color: colorSet.textColor,
                                                    border: `1px solid ${colorSet.accentColor}`,
                                                }}
                                            />
                                            <div className="d-flex gap-2">
                                                <Button
                                                    size="sm"
                                                    variant="success"
                                                    onClick={handleSaveEdit}
                                                    disabled={saving}
                                                >
                                                    <i className="fas fa-check mr-1"></i>{" "}
                                                    Save
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="secondary"
                                                    onClick={handleCancelEdit}
                                                >
                                                    <i className="fas fa-times mr-1"></i>{" "}
                                                    Cancel
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="d-flex justify-content-between align-items-center">
                                            <div
                                                style={{
                                                    flex: 1,
                                                    cursor: "pointer",
                                                }}
                                                onClick={() =>
                                                    handlePresetSelect(preset)
                                                }
                                            >
                                                <i
                                                    className="fas fa-comment-dots mr-2"
                                                    style={{
                                                        color: colorSet.accentColor,
                                                    }}
                                                ></i>
                                                {preset}
                                            </div>
                                            <div className="d-flex gap-1">
                                                <Button
                                                    size="sm"
                                                    variant="outline-warning"
                                                    onClick={() =>
                                                        handleEditPreset(index)
                                                    }
                                                    style={{
                                                        padding: "2px 8px",
                                                    }}
                                                >
                                                    <i className="fas fa-edit"></i>
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="outline-danger"
                                                    onClick={() =>
                                                        handleDeletePreset(
                                                            index,
                                                        )
                                                    }
                                                    style={{
                                                        padding: "2px 8px",
                                                    }}
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </Button>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}

                    {/* Add New Preset */}
                    <div
                        className="mt-3 pt-3"
                        style={{
                            borderTop: `1px solid ${colorSet.accentColor}`,
                        }}
                    >
                        <label
                            className="form-label"
                            style={{ fontWeight: "bold" }}
                        >
                            <i
                                className="fas fa-plus-circle mr-2"
                                style={{ color: colorSet.accentColor }}
                            ></i>
                            Add New Preset
                        </label>
                        <div className="input-group">
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Enter preset message..."
                                value={newPresetText}
                                onChange={(e) =>
                                    setNewPresetText(e.target.value)
                                }
                                onKeyPress={(e) => {
                                    if (
                                        e.key === "Enter" &&
                                        newPresetText.trim()
                                    ) {
                                        handleAddPreset();
                                    }
                                }}
                                maxLength={1000}
                                style={{
                                    backgroundColor: colorSet.chatBgColor,
                                    color: colorSet.textColor,
                                    border: `1px solid ${colorSet.accentColor}`,
                                }}
                            />
                            <Button
                                variant="success"
                                onClick={handleAddPreset}
                                disabled={!newPresetText.trim() || saving}
                            >
                                <i className="fas fa-plus mr-1"></i> Add
                            </Button>
                        </div>
                        <small className="text-muted">
                            Press Enter or click Add to save
                        </small>
                    </div>
                </Modal.Body>
                <Modal.Footer
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                        borderTop: `1px solid ${colorSet.accentColor}`,
                    }}
                >
                    <Button
                        variant="secondary"
                        onClick={() => setShowPresetsModal(false)}
                    >
                        Close
                    </Button>
                </Modal.Footer>
            </Modal>{" "}
        </StyledCard>
    );
};

export default FrostChatCard;
