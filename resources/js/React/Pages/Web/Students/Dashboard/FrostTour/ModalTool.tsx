import React from "react";

// Define prop types
interface ModalToolProps {
    data: any;
    modalPosition: {
        top: number;
        left: number;
    };
    spotlightPosition: {
        top: number;
        left: number;
        width: number;
        height: number;
    };
    setManualTop: (value: number) => void;
    setManualLeft: (value: number) => void;
    handlePositionChange: (
        spotlightPosition: any,
        manualTop: number,
        manualLeft: number
    ) => void;
}

const ModalTool: React.FC<ModalToolProps> = ({
    data,
    modalPosition,
    spotlightPosition,
    setManualTop,
    setManualLeft,
    handlePositionChange,
}) => {
    return (
        <div
            style={{
                position: "fixed",
                width: "450px",
                top: 0,
                left: 0,
                zIndex: 10021,
                backgroundColor: "white",
                padding: "10px",
            }}
        >
            {/* Display Current Step's Data */}
            <div>
                <h3>Current Step Data:</h3>
                <p><strong>Title:</strong> {data.title}</p>
                <p><strong>Selector:</strong> {data.selector}</p>
                <p><strong>Position:</strong> {data.position}</p>
                <hr />
            </div>

            {/* Display Modal Location and Dimensions */}
            <div>
                <h2>Modal Location and Dimensions</h2>
                <p><strong>Top:</strong> {modalPosition.top}px</p>
                <p><strong>Left:</strong> {modalPosition.left}px</p>
                <button className="btn btn-primary m-2" onClick={() => setManualTop(modalPosition.top + 10)}>
                    <i className="fa fa-arrow-down"></i>
                </button>
                <button className="btn btn-primary m-2" onClick={() => setManualLeft(modalPosition.left + 10)}>
                <i className="fa fa-arrow-right"></i>
                </button>
                <button className="btn btn-primary m-2" onClick={() => setManualTop(modalPosition.top - 10)}>
                    <i className="fa fa-arrow-up"></i>
                </button>
                <button className="btn btn-primary m-2" onClick={() => setManualLeft(modalPosition.left - 10)}>
                    <i className="fa fa-arrow-left"></i>
                </button>
                
            </div>

            {/* Display Spotlight Location and Dimensions */}
            <div>
                <h2>Spotlight Location & Dimensions</h2>
                <p><strong>Top:</strong> {spotlightPosition.top}px</p>
                <p><strong>Left:</strong> {spotlightPosition.left}px</p>
                <p><strong>Width:</strong> {spotlightPosition.width}px</p>
                <p><strong>Height:</strong> {spotlightPosition.height}px</p>
            </div>
        </div>
    );
};

export default ModalTool;

