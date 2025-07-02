/** @format */

import { useState, useCallback, useEffect } from "react";
import { AIAgentsConfigType } from "../Config/types";
import { sendMessage } from "./OpenAIClient";
import { Alert, Button, Card } from "react-bootstrap";

const masterAIName: string = "AIO";

const AIOTasks = {
  BaseRules: `
    -- Output: HTML format  
    -- Keep responses short but descriptive  
    -- Use bullet points for lists  
  `,

  HelloInstructor: `
    <b>Welcome, Instructor!</b>  
    <ul>
      <li>Assign AI as Assistant: Enable AI to assist with student queries, engagement, and course management.</li>
      <li>Disable AI: Keep manual control over course operations.</li>
    </ul>
    <p>AI can help with class management, automate administrative tasks, and improve student interaction.</p>
  `,
};

const useAIOHook = (aiConfig: AIAgentsConfigType, instructorId: number) => {
  const [aiResponse, setAiResponse] = useState<string | null>(
    aiConfig?.openai?.enable_ai
      ? `Waiting for ${masterAIName} response...`
      : `${masterAIName}: Offline`
  );

  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [show, setShow] = useState(true);

  /** ✅ Fetch response if progress is already recorded */
  const getResponseFromProgress = async (taskName: string, instructorId) => {
    try {
      const response = await fetch("/api/get-progress", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ instructor: instructorId, task: taskName }),
      });

      if (!response.ok) {
        throw new Error(`❌ Server responded with status: ${response.status}`);
      }

      const result = await response.json();
      return result.response || null;
    } catch (error) {
      console.error("❌ GetProgress Error:", error);
      return null;
    }
  };

  /** ✅ Sends request only if task is not completed */
  const sendTaskToAI = useCallback(
    async (taskName: string, instructorId: number) => {
      if (!aiConfig?.openai?.enable_ai) {
        console.warn(`${masterAIName} is disabled.`);
        setAiResponse(`${masterAIName}: Offline`);
        return;
      }

      setIsLoading(true);
      setError(null);

      try {
        const savedResponse = await getResponseFromProgress(
          taskName,
          instructorId
        );
        if (savedResponse) {
          console.log(`✅ Using saved response for task: ${taskName}`);
          setAiResponse(savedResponse);
          return;
        }

        const prompt = `${AIOTasks.BaseRules} ${
          AIOTasks[taskName] || `Perform task: ${taskName}`
        }`;
        console.log(`🟢 Sending AI Task: ${taskName}`);

        const response = await sendMessage({
          prompt,
          model: aiConfig.openai.default_model,
          temperature: aiConfig.openai.default_temperature,
          systemRole: aiConfig.openai.default_system_role,
        });

        setAiResponse(response);
        await recordInstructorProgress(taskName, response, instructorId);
      } catch (err) {
        console.error(`${masterAIName} Request Error:`, err);
        setError(`Failed to communicate with ${masterAIName}.`);
        setAiResponse(`${masterAIName}: Offline`);
      } finally {
        setIsLoading(false);
      }
    },
    [aiConfig]
  );

  /** ✅ Saves task response to progress */
  const recordInstructorProgress = async (
    task: string,
    response: string,
    instructorId: number
  ) => {
    const progress = {
      instructor: instructorId,
      task,
      response,
      status: "completed",
      time: new Date().toLocaleString(),
    };

    try {
      const result = await fetch("/api/write-progress", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(progress),
      });

      if (!result.ok) {
        throw new Error(`❌ Server responded with status: ${result.status}`);
      }

      console.log("✅ WriteProgress Success:", await result.json());
    } catch (error) {
      console.error("❌ WriteProgress Error:", error);
    }
  };

  /** ✅ Displays AI Status */
  const DisplayAlerts = () => {
    const [isCollapsed, setIsCollapsed] = useState(true);
    const [show, setShow] = useState(true);

    if (!show) return null;

    const isAIEnabled = aiConfig?.openai?.enable_ai ?? false;

    const headerText = isAIEnabled
      ? isCollapsed
        ? aiResponse?.slice(0, 50) + "..." || "AI Active"
        : `${masterAIName} is Online`
      : `${masterAIName} is Offline`;

    return (
      <Card className="mb-3 shadow-sm">
        <Card.Header
          className={`text-white d-flex justify-content-between align-items-center ${
            isAIEnabled ? "bg-info" : "bg-warning"
          }`}>
          <b>{headerText}</b>
          <div>
            <Button
              variant="outline-light"
              size="sm"
              onClick={() => setIsCollapsed(!isCollapsed)}>
              {isCollapsed ? "Expand" : "Collapse"}
            </Button>
            <Button
              variant="outline-danger"
              size="sm"
              className="ms-2"
              onClick={() => setShow(false)}>
              Close
            </Button>
          </div>
        </Card.Header>

        {!isCollapsed && (
          <Card.Body>
            <div className="d-flex justify-content-between align-items-center">
              <div
                dangerouslySetInnerHTML={{
                  __html: isAIEnabled
                    ? aiResponse || "Loading..."
                    : "AI is currently disabled.",
                }}
              />
            </div>
          </Card.Body>
        )}
      </Card>
    );
  };

  /** ✅ Auto-runs AI Greeting but skips if already completed */
  useEffect(() => {
    if (!aiConfig?.openai?.enable_ai) {
      console.warn("AIO is disabled.");
      setAiResponse(`${masterAIName}: Offline`);
      return;
    }

    sendTaskToAI("HelloInstructor", instructorId);
  }, [aiConfig?.openai?.enable_ai]);

  return {
    aiResponse,
    setAiResponse,
    isLoading,
    error,
    sendTaskToAI,
    DisplayAlerts,
  };
};

export default useAIOHook;
