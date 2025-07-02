import React, { useState, useEffect } from "react";
import { Alert } from "react-bootstrap";
import { MessageConsoleType } from "../../../../../Config/types";

const MessageConsole: React.FC<MessageConsoleType> = ({ status = null, message = null }) => {
  const [show, setShow] = useState(true);

  // Define the method to determine the display title based on the status
  const displayTitleStatus = (status: string) => {
    switch (status) {
      case "danger":
        return "Error";
      case "info":
        return "Information";
      case "success":
        return "Success";
      case "warning":
        return "Warning";
      default:
        return "Error";
    }
  };

  // Reset the auto-close timer whenever status or message changes
  useEffect(() => {
    setShow(true); // Make sure to show the alert again with a new message
    const timer = setTimeout(() => setShow(false), 5000); // Set to hide after 5 seconds
    return () => clearTimeout(timer); // Cleanup on unmount or before resetting the timer
  }, [status, message]); // Depend on status and message to reset the timer

  if (!show) return null; // Do not render the alert if show is false

  return (
    <Alert variant={status} onClose={() => setShow(false)} dismissible>
      <h4 className="text-white">{displayTitleStatus(status)}</h4>
      <p className="text-white">{message}</p>
    </Alert>
  );
};

export default MessageConsole;
