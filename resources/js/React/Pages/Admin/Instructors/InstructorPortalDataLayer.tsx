/** @format */
import React, { useState, useEffect } from "react";
import { Alert, Container } from "react-bootstrap";
import {
  AIAgentsConfigType,
  BaseLaravelShape,
  ValidatedInstructorShape,
} from "../../../Config/types";

import { ValidatedInstructorContext } from "../../../Context/Admin/ValidatedInstructorContext";
import InstructorClassRoom from "./FrostTraining/InstructorClassRoom";

import PageLoader from "../../../Components/Widgets/PageLoader";
import { useLaravelAdminHook } from "../../../Hooks/Admin/useLaravelAdminHook";
// import useAIOHook from "../../../Hooks/AIOHook";
import { useValidatedInstructorHook } from "../../../Hooks/Admin/useInstructorHooks";
import { sendMessage } from "../../../Hooks/OpenAIClient";

interface Props {
  debug: boolean;
}

const InstructorPortalDataLayer = ({ debug = false }: Props) => {
  if (debug) console.log("InstructorPortalDataLayer: ");

  const [laravel, setLaravel] = useState<BaseLaravelShape | null>(null);
  const [aiConfig, setAiConfig] = useState<AIAgentsConfigType | null>(null);
  const [aiInitialized, setAiInitialized] = useState<boolean>(false);

  // ✅ Get Laravel Admin Data
  const {
    data: laraData,
    isLoading,
    error,
  } = useLaravelAdminHook() as unknown as {
    data: BaseLaravelShape;
    isLoading: boolean;
    error: Error | null;
  };

  /**
   * This is the validated Instructor assigned to the course
   * @returns {Object} validatedInstructor
   */
  const {
    data: validatedInstructorData,
    status: vIStatus,
    error: vIError,
  } = useValidatedInstructorHook();

  const defaultAIConfig: AIAgentsConfigType = {
    openai: {
      enable_ai: false,
      api_key: "",
      org_id: "",
      url: "",
      default_model: "",
      default_system_role: "",
      default_temperature: 0,
    },
    write_progress: {
      file_path: "",
      default_message: "",
    },
  };

  // ✅ Ensure Hook is always called
  // const AIO = useAIOHook(aiConfig ?? defaultAIConfig, validatedInstructorData?.instructor?.id);

  // ✅ Use useEffect to update state when data is fetched
  useEffect(() => {
    console.log("laraData", laraData);
    if (laraData?.success) {
      setLaravel(laraData);
      if (laraData.config?.aiagents) {
        setAiConfig(laraData.config.aiagents);
      }
    }
  }, [laraData]);

  /**
   * Fetch AI Response on Component Mount
   * if greeting in progress, don't send more greetings
   */
  // useEffect(() => {
  //   if (!validatedInstructorData || !aiConfig?.openai?.enable_ai || aiInitialized) {
  //     console.warn("AI is disabled, instructor data is invalid, or already initialized.");
  //     return;
  //   }

  //   console.log("validatedInstructorDataAfter", validatedInstructorData);

  //   const fetchAIResponse = async (validatedInstructorData: ValidatedInstructorShape) => {
  //     try {
  //       const instructorName =
  //         validatedInstructorData?.instructor.fname +
  //         " " +
  //         validatedInstructorData?.instructor.lname;

  //       const response = await sendMessage({
  //         prompt: `HelloInstructor: ${instructorName}`,
  //       });

  //      // AIO.setAiResponse(response);
  //      // AIO.recordInstructorProgress("HelloInstructor", response);

  //       setAiInitialized(true); // ✅ Prevent re-initializing AI on re-renders
  //     } catch (error) {
  //       console.error("AI Error:", error);
  //      // AIO.setAiResponse("Error retrieving AI response.");
  //     }
  //   };

  //   fetchAIResponse(validatedInstructorData);
  // }, [validatedInstructorData, aiConfig?.openai?.enable_ai]); // ✅ Only re-run when AI config changes

  if (vIStatus === "loading" || isLoading)
    return <PageLoader base_url={window.location.origin} />;

  if (vIError || error) {
    return (
      <Alert variant="danger">
        Error loading data:{" "}
        {(vIError as Error)?.message || (error as Error)?.message}
      </Alert>
    );
  }

  return (
    <>
      {/* {AIO.DisplayAlerts() || null} */}

      <Container fluid>
        <ValidatedInstructorContext.Provider value={validatedInstructorData}>
          {laravel !== null && (
            <InstructorClassRoom laravel={laravel} debug={debug} />
          )}
        </ValidatedInstructorContext.Provider>
      </Container>
    </>
  );
};

export default InstructorPortalDataLayer;
