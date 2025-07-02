import React, { useEffect, useRef, useState } from "react";
import { Button, Modal } from "react-bootstrap";
import ModalTool from "./ModalTool";
import SpotLight from "./SpotLight";
import { debounce } from "../../../../../Config/utils";
import "./modal-reset.css";
import { BsPrefixComponent } from "react-bootstrap/esm/helpers";
import { set } from "lodash";

// Define prop types for SpotLightTour
interface HelpDataItem {
    selector: string;
    position?: string;
    title: string;
    description: string;
    width: number;
    height: number;
}

interface SpotLightTourProps {
    HelpData: HelpDataItem[];
    currentStep: number;
    nextStep: () => void;
    prevStep: () => void;
    isHelpModalOpen: boolean;
    closeHelp: () => void;
    modalPosition: { top: number; left: number }; // Again, adjust based on actual usage
    setModalPosition: React.Dispatch<
        React.SetStateAction<{ top: number; left: number }>
    >;
    modalDirection?: "bottom" | "top" | "left" | "right" | "center";
}

const SpotLightTour = ({
    HelpData, // This is an array of objects
    currentStep, // This the currrent step of the array
    nextStep, // This is a function that increments the current step
    prevStep, // This is a function that decrements the current step
    isHelpModalOpen, // This is a boolean that determines whether the modal is open
    closeHelp, // This is a function that closes the modal
    modalPosition, // This is an object that contains the top and left positions of the modal
    setModalPosition, // This is a function that sets the top and left positions of the modal
    modalDirection = "bottom", // Default to 'bottom'
}: SpotLightTourProps) => {
    /**
     * DEVTOOLS
     */
    const isEnableDevTools = false;
    const [manualTop, setManualTop] = useState<number | null>(null);
    const [manualLeft, setManualLeft] = useState<number | null>(null);

    interface SpotlightPositionProps {
        top: number;
        left: number;
        right: number;
        bottom: number;
        width: number;
        height: number;
    }

    const [spotlightPosition, setSpotlightPosition] =
        React.useState<SpotlightPositionProps>({
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            width: 0,
            height: 0,
        });

    // This is the area where the modal will be displayed
    const [screenWidth, setScreenWidth] = useState<number>(0);
    const [screenHeight, setScreenHeight] = useState<number>(0);

    const [modalWidth, setModalWidth] = useState<number>(0);
    const [modalHeight, setModalHeight] = useState<number>(0);

    const getCurrentStepHelpData = () => {
        const currentHelpData = HelpData[currentStep];
        const [vertPosition, horPosition] = (
            currentHelpData.position || "center:center"
        ).split(":");

        console.log("currentHelpData: ", currentHelpData);

        return {
            ...currentHelpData,
            vertPosition,
            horPosition,
        };
    };

    const resetModalPosition = () => {
        setModalPosition({
            top: 0,
            left: 0,
        });
    };

    const getSpotlightPositionFromDOM = async (selector) => {
        const element = await document.querySelector(selector);

        if (!element) {
            console.warn(`Element not found for selector: ${selector}`);
            return {
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                width: 0,
                height: 0,
            };
        }

        return element.getBoundingClientRect();
    };

    const handleSpotlightPosition = async () => {
        const spotlightData = HelpData[currentStep];
        const position = await getSpotlightPositionFromDOM(
            spotlightData.selector
        );

        console.log("SelectSpotlightPositionData: ", position);
        setSpotlightPosition(position);
    };

    const getModalDimensions = () => {
        const { width, height } = HelpData[currentStep];

        setModalWidth(width);
        setModalHeight(height);

        return {
            width: width,
            height: height,
        };
    };

    const handleModalPositionChange = (
        spotlightPosition,
        manualTopOffset,
        manualLeftOffset
    ) => {
        if (!spotlightPosition) return;

        let finalModalTop = 0;
        let finalModalLeft = 0;

        /**
         * Get the vertical and horizontal positions from the current step's HelpData
         * i.e "top:center" being where the modal should be positioned relative to the spotlight
         * vertical: top, center, bottom
         * horizontal: left, center, right
         */
        const { vertPosition, horPosition } = getCurrentStepHelpData();

        /**
         * Maps the Modal position Y in relation to the spotlight
         */
        const vertPositionMap = {
            top: spotlightPosition.top,
            center: spotlightPosition.top + spotlightPosition.height / 2,
            bottom: spotlightPosition.bottom,
        };

        /**
         * Maps the Modal position X in relation to the spotlight
         */
        const horPositionMap = {
            left: spotlightPosition.left - spotlightPosition.width,
            center: spotlightPosition.left + spotlightPosition.width / 2,
            right: spotlightPosition.right,
        };

        /**
         * Calculate the modal position based on the spotlight position and specified position
         */
        let calculatedModalTop = vertPositionMap[vertPosition];
        let calculatedModalLeft = horPositionMap[horPosition];

        // If Calculated Modal Top is less than 0, set it to 0
        if (calculatedModalLeft < 0) {
            calculatedModalLeft = 0;
        }
        if (calculatedModalTop < 0) {
            calculatedModalTop = 0;
        }

        console.log(
            "BeforeModalIsOffScreen",
            "Modal Width: " + modalWidth,
            calculatedModalLeft + modalWidth,
            screenWidth
        );
        if (calculatedModalLeft + modalWidth > screenWidth) {
            console.log(
                "ModalIsOffScreen",
                calculatedModalLeft + modalWidth,
                screenWidth
            );

            calculatedModalLeft = screenWidth - modalWidth - 20;
        }

        if (calculatedModalTop + modalHeight > screenHeight) {
            console.log(
                "ModalIsOffScreen",
                calculatedModalTop + modalHeight,
                screenHeight
            );

            calculatedModalTop = calculatedModalTop - modalHeight - 20;
        }

        finalModalLeft = calculatedModalLeft;
        finalModalTop = calculatedModalTop;

        setModalPosition({
            top: finalModalTop,
            left: finalModalLeft,
        });

        // Ensure the target div is scrolled into view if it is below the screen
        console.log("HelpData: ", HelpData[currentStep].selector);
        // const targetDiv = document.querySelector(
        //     HelpData[currentStep].selector
        // ); // Replace 'your-target-div-id' with the actual id of your target div
        // if (targetDiv) {
        //     targetDiv.scrollIntoView({
        //         behavior: "smooth",
        //         block: "nearest",
        //         inline: "nearest",
        //     });
        // }
    };

    useEffect(() => {
        resetModalPosition();

        getSpotlightPositionFromDOM(HelpData[currentStep].selector).then(
            (position) => {
                setSpotlightPosition(position);
            }
        );

        setScreenWidth(window.innerWidth);
        setScreenHeight(window.innerHeight);
    }, []);

    useEffect(() => {
        const modal = getModalDimensions();
        console.log("modal: ", modal);
        setModalWidth(modal.width);
        setModalHeight(modal.height);
    }, []);

    useEffect(() => {
        handleSpotlightPosition();
    }, [currentStep]);

    useEffect(() => {
        handleModalPositionChange(spotlightPosition, manualTop, manualLeft);
    }, [modalWidth, modalHeight]);

    /**
     * Updates the modal position when the spotlight position changes
     */
    useEffect(() => {
        handleModalPositionChange(spotlightPosition, manualTop, manualLeft);
    }, [spotlightPosition, manualTop, manualLeft]);

    /**
     * The Modal
     */
    return (
        <>
            {isEnableDevTools && (
                <ModalTool
                    data={HelpData[currentStep]}
                    modalPosition={modalPosition}
                    spotlightPosition={spotlightPosition}
                    setManualTop={setManualTop}
                    setManualLeft={setManualLeft}
                    handlePositionChange={handleModalPositionChange}
                />
            )}
            <SpotLight
                targetSelector={HelpData[currentStep].selector}
                handleSpotlightPosition={handleSpotlightPosition}
                modalPosition={modalPosition}
                setModalPosition={setModalPosition}
                spotlightPosition={spotlightPosition as DOMRect | null}
            />

            <Modal
                show={isHelpModalOpen}
                backdrop={false}
                onHide={closeHelp}
                style={{
                    position: "absolute",
                    top: modalPosition.top + "px",
                    left: modalPosition.left + "px",
                    width: modalWidth + "px",
                    height: modalHeight + "px",
                    transform: "none",
                    marginRight: "auto",
                    marginBottom: "auto",
                    transition: "all 0.5s ease",
                    zIndex: 10011,
                }}
            >
                <Modal.Header closeButton>
                    <Modal.Title>{HelpData[currentStep].title}</Modal.Title>
                </Modal.Header>
                <Modal.Body>{HelpData[currentStep].description}</Modal.Body>
                <Modal.Footer>
                    {/* Close Button on the left */}
                    <Button
                        variant="dark"
                        onClick={closeHelp}
                        className="d-flex align-items-start"
                    >
                        Close
                    </Button>

                    {/* Rest of the buttons remain on the right */}
                    <div className="ms-auto">
                        {" "}
                        {/* This div will push the remaining buttons to the right */}
                        <Button
                            variant="secondary"
                            onClick={prevStep}
                            disabled={currentStep === 0}
                        >
                            Previous
                        </Button>
                        <Button variant="primary" onClick={nextStep}>
                            {currentStep ===
                            (HelpData ? HelpData.length - 1 : 0)
                                ? "Finish"
                                : "Next"}
                        </Button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    );
};

export default React.memo(SpotLightTour);
