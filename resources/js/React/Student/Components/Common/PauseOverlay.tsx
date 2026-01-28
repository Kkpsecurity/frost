import React from "react";

const PauseOverlay = ({
    pauseRemainingSeconds,
}: {
    pauseRemainingSeconds: number;
}) => {
    return (
        <>
            {pauseRemainingSeconds > 0 && (
                <div
                    style={{
                        position: "fixed",
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        backgroundColor: "rgba(0, 0, 0, 0.3)",
                        zIndex: 9998,
                        pointerEvents: "auto",
                    }}
                />
            )}
        </>
    );
};

export default PauseOverlay;
