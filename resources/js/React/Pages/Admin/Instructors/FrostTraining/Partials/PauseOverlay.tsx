import React, { useState } from "react";

interface PausedProps {
    handleClose: Function | false;
    isPaused: boolean;
}

const PauseOverlay = ({ handleClose, isPaused }: PausedProps) => {
    return (
        <div>
            {isPaused && (
                <div
                    style={{
                        position: "fixed",
                        top: 0,
                        left: 0,
                        width: "100%",
                        height: "100%",
                        backgroundColor: "rgba(140, 0, 0, 0.7)",
                        zIndex: 4000,
                        objectFit: "cover",
                    }}
                >
                    <div
                        style={{
                            position: "absolute",
                            top: "30%",
                            left: "50%",
                            transform: "translate(-50%, -50%)",
                            backgroundColor: "white",
                            padding: "20px",
                            borderRadius: "5px",
                        }}
                    >
                        <h2>Lesson Paused</h2>
                        <p>
                            The instructor has paused the lesson. Please be
                            patient and wait for the class to resume.
                        </p>

                        {handleClose !== false && (
                            <button
                                className="btn btn-success btn-block"
                                onClick={() => handleClose()}
                            >
                                Resume
                            </button>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

export default PauseOverlay;
