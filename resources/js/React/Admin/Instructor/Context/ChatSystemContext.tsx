import React, { createContext, useContext, ReactNode } from "react";

export interface ChatSystemContextType {
    messages: any[];
    isConnected: boolean;
    loading: boolean;
    error: string | null;
    lastUpdated: Date | null;
}

const ChatSystemContext = createContext<ChatSystemContextType | undefined>(undefined);

interface ChatSystemContextProviderProps {
    children: ReactNode;
    value: ChatSystemContextType;
}

export const ChatSystemContextProvider: React.FC<ChatSystemContextProviderProps> = ({
    children,
    value,
}) => (
    <ChatSystemContext.Provider value={value}>
        {children}
    </ChatSystemContext.Provider>
);

export const useChatSystemContext = (): ChatSystemContextType => {
    const context = useContext(ChatSystemContext);
    if (!context) {
        throw new Error("useChatSystemContext must be used within ChatSystemContextProvider");
    }
    return context;
};
