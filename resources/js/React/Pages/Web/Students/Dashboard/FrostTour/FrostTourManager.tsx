import React, { useState, useEffect, useContext } from "react";
import SpotLightTour from "./SpotLightTour";
import { LiveClassHelpData, OfflineClassHelpData } from "./data/data";
import { SpotlightTourContext } from "../../../../../Context/SpotLightContext";

/**
 * External: Trigger the help tour from outside the component
 * Purpose: Starts the help tour by setting the modal to open.
 * @param {Function} setIsHelpModalOpen - Setter function for modal state
 */
const beginHelp = (setIsHelpModalOpen) => {
    setIsHelpModalOpen(true);
};

// Define prop types for SpotLightTour
interface HelpDataItem {
    selector: string;
    position?: string;
    title: string;
    description: string;
    width: number;
    height: number;
}

const FrostTourManager = ({ classData }) => {
    const [modalPosition, setModalPosition] = useState({ top: 0, left: 0 });
    const [currentStep, setCurrentStep] = useState(0);
    const [HelpData, setHelpData] = useState<HelpDataItem[] | null>(null);

    const { isHelpModalOpen, setIsHelpModalOpen } = useContext(SpotlightTourContext);

    /**
     * Internal: Navigate to the next help step or close the modal if it's the last step
     */
    const nextStep = () => {
        const isLastStep = currentStep >= HelpData.length - 1;

        if (isLastStep) {
            closeHelp();
        } else {
            setCurrentStep(prevStep => prevStep + 1);
        }
    };

    /**
     * Internal: Navigate to the previous help step
     */
    const prevStep = () => {
        const isFirstStep = currentStep <= 0;

        if (!isFirstStep) {
            setCurrentStep(prevStep => prevStep - 1);
        }
    };

    /**
     * Internal: Close the help modal and reset the tour
     */
    const closeHelp = () => {
        setIsHelpModalOpen(false);
    };

    /**
     * Internal: Determine which set of help data to use based on the provided class data
     */
    useEffect(() => {
        // Ensure classData is defined before setting HelpData
        if (!classData) {
            setHelpData(null); // Or set to a default HelpData if preferable
            return;
        }

        if (classData.courseDate?.id) {
            setHelpData(LiveClassHelpData);
        } else {
            setHelpData(OfflineClassHelpData);
        }
    }, [classData]);

    /**
     * Internal: Reset the current step to 0 when the help modal is closed
     */
    useEffect(() => {
        setCurrentStep(0);
    }, [isHelpModalOpen]);

    return (
        isHelpModalOpen && (
            <SpotLightTour
                HelpData={HelpData}
                currentStep={currentStep}
                nextStep={nextStep}
                prevStep={prevStep}
                isHelpModalOpen={isHelpModalOpen}
                closeHelp={closeHelp}
                modalPosition={modalPosition}
                setModalPosition={setModalPosition}
                modalDirection="right"
            />
        )
    );
};

export default FrostTourManager;
export { beginHelp };
