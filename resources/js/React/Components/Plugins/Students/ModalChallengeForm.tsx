import React, { useEffect } from "react";
import CaptureCode from "../../FormElements/CaptureCode";
import VerifyBox from "../../FormElements/VerifyBox";
import { Col, Form, Row } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";

import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

interface ModalChallengeFormProps {
  verifyCode: any;
  isFinal: boolean;
  isEOL: boolean;

}

const ModalChallengeForm: React.FC<ModalChallengeFormProps> = ({
  verifyCode,
  isFinal,
  isEOL,
}) => {
  const methods = useForm();

  /**
   * Reset the value of each VerifyBox component to an empty string ("") or null
   */
  const resetVerifyBoxes = () => {
    // Reset the value of each VerifyBox component to an empty string ("") or null
    Array.from({ length: 4 }, (_, i) => {
      methods.setValue(`code_${i + 1}`, "");
    });
  };

  // Call the resetVerifyBoxes function when the component mounts or updates
  useEffect(() => {
    resetVerifyBoxes();
  }, []);

  const verifyTheCode = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const formData = new FormData(e.target as HTMLFormElement);
    const enteredCode = Array.from({ length: 4 }, (_, i) =>
      parseInt(formData.get(`code_${i + 1}`) as string)
    ).join("");
    const storedCode =
      localStorage.getItem("generatedNumbers")?.replace(/[\[\]]/g, "").split(",") ?? [];
    const storedCodeString = storedCode.map((code) => parseInt(code)).join("");

    if (enteredCode === storedCodeString) {
      verifyCode();
    } else {
      toast.error("The code you entered is incorrect. Please try again.");
    }
  };


  return (
    <div>
        <ToastContainer />
        <FormProvider {...methods}>
        <CaptureCode />
        <hr />
        <Form
          id="verification_form"
          className="form-horizontal p-1"
          method="POST"
          onSubmit={verifyTheCode}
        >
          <Row>
            <VerifyBox index={1} />
            <VerifyBox index={2} />
            <VerifyBox index={3} />
            <VerifyBox index={4} />
          </Row>
          <Row>
            <Col className="d-flex justify-content-center p-3">
              <button
                type="submit"
                className="btn btn-success btn-block"
                id="submit_verification"
              >
                Submit Challenge
              </button>
            </Col>
          </Row>
        </Form>
      </FormProvider>
    </div>
  );
};

export default ModalChallengeForm;
