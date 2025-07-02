import React, { useEffect, useState } from "react";

interface SidebarCollapseHandlerProps {
    setSidebarVisible: React.Dispatch<React.SetStateAction<boolean>>;
    sidebarVisible: boolean;
    setSidebarHidden: React.Dispatch<React.SetStateAction<boolean>>;
    sidebarHidden: boolean;
}

const sidebarCollapseHandler = ({
    setSidebarVisible,
    sidebarVisible,
    setSidebarHidden,
    sidebarHidden,
}: SidebarCollapseHandlerProps) => {
    
    /**
     * Toggle the sidebar visibility  (show/hide)
     */
    const toggleSidebarVisibility = () => {
        const newSidebarVisible = !sidebarVisible;
        setSidebarVisible(newSidebarVisible);
        localStorage.setItem(
            "sidebarVisible",
            JSON.stringify(newSidebarVisible)
        );
    };

    /**
     * Handle the window resize event
     */
    const handleWindowResize = () => {
        const resizeHandler = () => {
            const width = window.innerWidth;
            if (width <= 576) {
                // For width <= 720px, sidebar should be hidden
                setSidebarHidden(true);
                setSidebarVisible(false);
            } else if (width > 720 && width <= 960) {
                // For 578px < width <= 768px, sidebar should auto-collapse
                setSidebarHidden(false);
                setSidebarVisible(false);
            } else {
                // For width > 768px, restore the original sidebar visibility
                setSidebarHidden(false);
                setSidebarVisible(true);
            }
        };

        window.addEventListener("resize", resizeHandler);
        return resizeHandler;
    };

    useEffect(() => {
        const savedSidebarVisible = localStorage.getItem("sidebarVisible");
        if (savedSidebarVisible) {
            setSidebarVisible(JSON.parse(savedSidebarVisible));
        }
    }, [setSidebarVisible]);

    useEffect(() => {
        const resizeCallback = handleWindowResize();
        window.addEventListener("resize", resizeCallback);

        return () => {
            window.removeEventListener("resize", resizeCallback);
        };
    }, [setSidebarVisible, setSidebarHidden]);

    return {
        toggleSidebarVisibility,
        handleWindowResize,
        sidebarHidden,
    };
};

export default sidebarCollapseHandler;
