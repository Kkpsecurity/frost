import React, { useEffect, useState } from "react";
import styled from "styled-components";
import { Card, Button } from "react-bootstrap";

interface CanvasComponentProps {
    width: number;
    height: number;
    captured: boolean;
    canvasRef;
    handleSaveImage: () => void;
    handleWebcamReset: () => void;
    debug?: boolean;
}

const CanvasContainer = styled.div`
    width: ${(props) => props.width}px;
    margin: 0;
    padding: 0;
    background: #444;
    display: ${(props) => (props.captured ? "block" : "none")};

    canvas {
        width: 100%;
        height: 100%;
    }

    @media (max-width: 768px) {
        width: 100%;
    }

    @media (max-width: 576px) {
        width: 100%;
    }

    @media (max-width: 320px) {
        width: 100%;
    }
`;

const ButtonContainer = styled.div`
    display: flex;
    justify-content: space-between;
    padding: 1.5rem;
`;

const CanvasComponent: React.FC<CanvasComponentProps> = ({
    width,
    height,
    captured,
    canvasRef,
    handleSaveImage,
    handleWebcamReset,
    debug = false,
}) => {
    if (debug === true)
        console.log("CanvasComponent", width, height, captured, canvasRef);

    return (
        <>
            <CanvasContainer width={width} captured={captured}>
                <canvas
                    ref={canvasRef}
                    width={width}
                    height={height}
                    style={{
                        margin: 0,
                    }}
                />
            </CanvasContainer>
            {captured && (
                <div>
                    <ButtonContainer>
                        <Button
                            variant="primary"
                            size="lg"
                            onClick={handleSaveImage}
                        >
                            Save
                        </Button>
                        <Button
                            variant="danger"
                            size="lg"
                            onClick={handleWebcamReset}
                        >
                            Reset
                        </Button>
                    </ButtonContainer>
                </div>
            )}
        </>
    );
};

export default CanvasComponent;
