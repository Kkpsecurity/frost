import { ca } from "date-fns/locale";
import React, { useEffect } from "react";

type SpotlightPositionProps = {
    top: number;
    left: number;
    width: number;
    height: number;
};

// Define prop types for Spotlight
interface SpotlightProps {
    targetSelector: string;
    handleSpotlightPosition: (rect: DOMRect | DOMRectReadOnly) => void;
    modalPosition: { top: number; left: number };
    setModalPosition: React.Dispatch<
        React.SetStateAction<{ top: number; left: number }>
    >;
    spotlightPosition: DOMRect | null;
}

const SpotLight: React.FC<SpotlightProps> = ({
    targetSelector,
    handleSpotlightPosition,
    modalPosition,
    setModalPosition,
}) => {
    const [position, setPosition] = React.useState<DOMRect | null>(null);

    const prevTargetRef = React.useRef<HTMLElement | null>(null);  // <-- Maintaining reference to the previous target

    useEffect(() => {
        // If there was a previous target, reset its z-index
        if (prevTargetRef.current) {
            prevTargetRef.current.style.zIndex = '';
        }

        const target = document.querySelector(targetSelector) as HTMLElement;
        if (!target) return;
        
        const rect = target.getBoundingClientRect();
        setPosition(rect);
        handleSpotlightPosition(rect);
    
        target.style.zIndex = "10100"; // Setting z-index for the new target element
        prevTargetRef.current = target;  // <-- Storing the current target as the previous one for the next time
    
    }, [targetSelector]);
    
    const calcZIndex = (rect: DOMRect | null) => {
        if (!rect) return 0;
        const { top, left, width, height } = rect;
        const { innerHeight, innerWidth } = window;
        const maxDimension = Math.max(innerHeight, innerWidth);
        const minDimension = Math.min(innerHeight, innerWidth);
        const maxDimensionRatio = maxDimension / minDimension;
        const minDimensionRatio = minDimension / maxDimension;
        const topRatio = top / minDimension;
        const leftRatio = left / maxDimension;
        const widthRatio = width / maxDimension;
        const heightRatio = height / minDimension;
        const zIndex = Math.round(
            10000 * (topRatio + leftRatio + widthRatio + heightRatio)
        );
        return zIndex;
    };

    const overlayStyle: React.CSSProperties = {
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        background: "rgba(0, 0, 0, 0.8)", // Black semi-opaque background
        zIndex: 9995,
    };

    const spotlightStyle = position ? {
        position: 'fixed' as 'fixed', // TypeScript doesn't like the string value
        top: position.top + 'px',
        left: position.left + 'px',
        width: position.width + 'px',
        height: position.height + 'px',       
        zIndex: calcZIndex(position),
    } : {};
    

    return (
        <>
            <div style={overlayStyle}></div>
            {position && <div style={spotlightStyle}></div>}
        </>
    );
};

export default SpotLight;

