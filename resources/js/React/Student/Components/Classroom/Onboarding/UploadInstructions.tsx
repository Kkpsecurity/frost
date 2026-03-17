import React from "react";
import styled from "styled-components";
import InstructionMessage from "./InstructionMessage";
import { t } from "@/i18n";

const InstructionContainer = styled.div`
    font-family: Arial, sans-serif;
    padding: 20px;
    background-color: #34495e;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
    margin: 20px auto;
    border-radius: 8px;
`;

const InstructionHeader = styled.h2`
    color: #ecf0f1;
    font-size: 1.5rem;
    text-transform: uppercase;
    padding-bottom: 10px;
    margin-bottom: 15px;
    border-bottom: 2px solid #3498db;
`;

const SubHeader = styled.h3`
    color: #3498db;
    font-size: 1.2rem;
    text-transform: uppercase;
    padding-bottom: 10px;
    margin-bottom: 15px;
    margin-top: 20px;
`;

const InstructionList = styled.ul`
    list-style-type: disc;
    font-size: 1rem;
    margin-left: 20px;
    color: #bdc3c7;
`;

const InstructionItem = styled.li`
    margin-bottom: 10px;
    color: #ecf0f1;
`;

const InstructionText = styled.p`
    color: #ecf0f1;
    font-size: 1rem;
    line-height: 1.5;
    margin-top: 20px;
    padding: 15px;
    background-color: rgba(52, 152, 219, 0.1);
    border-left: 4px solid #3498db;
    border-radius: 4px;
    strong {
        color: #3498db;
    }
`;

interface UploadInstructionsProps {
    validations?: any;
}

const UploadInstructions: React.FC<UploadInstructionsProps> = ({
    validations,
}) => {

    return (
        <InstructionContainer>
            {validations?.message && (
                <InstructionMessage validations={validations} />
            )}
            <InstructionHeader>
                {t('onboarding.validationInstructions')}
            </InstructionHeader>
            <SubHeader>{t('onboarding.stepUploadHeadshot')}</SubHeader>
            <InstructionList>
                <InstructionItem>
                    {t('onboarding.headshotLighting')}
                </InstructionItem>
                <InstructionItem>
                    {t('onboarding.headshotNoAccessories')}
                </InstructionItem>
                <InstructionItem>
                    {t('onboarding.headshotCentered')}
                </InstructionItem>
            </InstructionList>
            <SubHeader>{t('onboarding.stepUploadIdCard')}</SubHeader>
            <InstructionList>
                <InstructionItem>
                    {t('onboarding.idCardFlat')}
                </InstructionItem>
                <InstructionItem>
                    {t('onboarding.idCardNoGlare')}
                </InstructionItem>
                <InstructionItem>
                    {t('onboarding.idCardNameMatch')}
                </InstructionItem>
            </InstructionList>
            <InstructionText>
                <strong>Note:</strong> {t('onboarding.validationNote')}
            </InstructionText>
        </InstructionContainer>
    );
};

export default UploadInstructions;
