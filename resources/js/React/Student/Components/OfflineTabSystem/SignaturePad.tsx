import React, { useRef, useState, useEffect } from "react";
import { Button } from "react-bootstrap";
import { t } from "@/i18n";

interface SignaturePadProps {
    onSave: (dataUrl: string) => void;
    onClear?: () => void;
    width?: number;
    height?: number;
    penColor?: string;
    backgroundColor?: string;
}

const SignaturePad: React.FC<SignaturePadProps> = ({
    onSave,
    onClear,
    width = 400,
    height = 200,
    penColor = "#000000",
    backgroundColor = "#ffffff",
}) => {
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [isEmpty, setIsEmpty] = useState(true);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        // Set canvas background
        ctx.fillStyle = backgroundColor;
        ctx.fillRect(0, 0, width, height);

        // Set drawing properties
        ctx.strokeStyle = penColor;
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
    }, [width, height, penColor, backgroundColor]);

    const startDrawing = (
        e:
            | React.MouseEvent<HTMLCanvasElement>
            | React.TouchEvent<HTMLCanvasElement>,
    ) => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        const rect = canvas.getBoundingClientRect();
        const x =
            "touches" in e
                ? e.touches[0].clientX - rect.left
                : e.clientX - rect.left;
        const y =
            "touches" in e
                ? e.touches[0].clientY - rect.top
                : e.clientY - rect.top;

        ctx.beginPath();
        ctx.moveTo(x, y);
        setIsDrawing(true);
        setIsEmpty(false);
    };

    const draw = (
        e:
            | React.MouseEvent<HTMLCanvasElement>
            | React.TouchEvent<HTMLCanvasElement>,
    ) => {
        if (!isDrawing) return;

        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        const rect = canvas.getBoundingClientRect();
        const x =
            "touches" in e
                ? e.touches[0].clientX - rect.left
                : e.clientX - rect.left;
        const y =
            "touches" in e
                ? e.touches[0].clientY - rect.top
                : e.clientY - rect.top;

        ctx.lineTo(x, y);
        ctx.stroke();
    };

    const stopDrawing = () => {
        if (!isDrawing) return;

        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        ctx.closePath();
        setIsDrawing(false);
    };

    const clearCanvas = () => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        if (!ctx) return;

        ctx.fillStyle = backgroundColor;
        ctx.fillRect(0, 0, width, height);
        setIsEmpty(true);

        if (onClear) {
            onClear();
        }
    };

    const saveSignature = () => {
        const canvas = canvasRef.current;
        if (!canvas || isEmpty) return;

        const dataUrl = canvas.toDataURL("image/png");
        onSave(dataUrl);
    };

    return (
        <div className="signature-pad-container">
            <div
                style={{
                    border: "2px solid #34495e",
                    borderRadius: "0.5rem",
                    padding: "0.5rem",
                    backgroundColor: "#ecf0f1",
                    display: "inline-block",
                }}
            >
                <canvas
                    ref={canvasRef}
                    width={width}
                    height={height}
                    onMouseDown={startDrawing}
                    onMouseMove={draw}
                    onMouseUp={stopDrawing}
                    onMouseLeave={stopDrawing}
                    onTouchStart={startDrawing}
                    onTouchMove={draw}
                    onTouchEnd={stopDrawing}
                    style={{
                        display: "block",
                        touchAction: "none",
                        cursor: "crosshair",
                        borderRadius: "0.25rem",
                    }}
                />
            </div>

            <div className="mt-3 d-flex gap-2">
                <Button
                    variant="outline-light"
                    size="sm"
                    onClick={clearCanvas}
                    disabled={isEmpty}
                >
                    <i className="fas fa-eraser me-1"></i>
                    {t("offlineTab.clear")}
                </Button>
                <Button
                    variant="success"
                    size="sm"
                    onClick={saveSignature}
                    disabled={isEmpty}
                >
                    <i className="fas fa-save me-1"></i>
                    {t("offlineTab.saveSignature")}
                </Button>
            </div>
        </div>
    );
};

export default SignaturePad;
