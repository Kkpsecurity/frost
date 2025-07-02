import React from "react";
import { Button, Form } from "react-bootstrap";
import { ErrorBoundary } from "react-error-boundary";
import { FormProvider, useForm } from "react-hook-form";
import { StudentType } from "../../../../Config/types";

import { AlertFormFallback } from "../../../../ErrorHandeling/AlertFormFallback";

import Password from "../../../../Components/FormElements/Password";
import PasswordWPB from "../../../../Components/FormElements/PasswordWPB";
import TextHidden from "../../../../Components/FormElements/TextHidden";

import * as Yup from "yup";
import { useUpdatePassword } from "../../../../Hooks/Web/useProfileManager";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

interface Props {
  user_id: number;
}

interface PasswordFormData {
  user_id: number;
  old_password: string;
  password: string;
  password_confirmation: string;
}

const EditPasswordForm: React.FC<Props> = ({ user_id }) => {
  /**
   * Prepare the Form Hook
   */
  const methods = useForm<PasswordFormData>();

  const passwordSchema = Yup.object().shape({
    old_password: Yup.string().required("Current Password is required"),
    password: Yup.string()
      .required("Password is required")
      .min(8, "Password must be at least 8 characters long")
      .matches(
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])/,
        "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character"
      ),
    password_confirmation: Yup.string()
      .required("Confirm Password is required")
      .oneOf([Yup.ref("password"), null], "Passwords must match"),
  });

  const { mutate: updatePasswordMutate } = useUpdatePassword();

  interface UpdatePasswordRequest {
    user_id: number;
    old_password: string;
    password: string;
    password_confirmation: string;
  }
  
  const updatePassword = async (data: PasswordFormData) => {
    try {
      // Validate the form values
      await passwordSchema.validate(data, { abortEarly: false });
  
      // Update the password in the database
      const updatePasswordRequest: UpdatePasswordRequest = {
        user_id: data.user_id,
        old_password: data.old_password,
        password: data.password,
        password_confirmation: data.password_confirmation,
      };
      await updatePasswordMutate(updatePasswordRequest);
      toast.success("Password Updated Successfully");
    } catch (err) {
      // If the validation fails, display the errors
      console.error(err);
      toast.error("Password Update Failed");
    }
  };
  

  return (
    <FormProvider {...methods}>
      <ErrorBoundary FallbackComponent={AlertFormFallback}>
        <Form
          action="#"
          method="post"
          className="form p-3 bg-gray-500"
          role="form"
          autoComplete={"off"}
          onSubmit={methods.handleSubmit(updatePassword)}
        >
          <TextHidden id="user_id" value={user_id} />

          <Password
            id="old_password"
            title="Current Password"
            required={true}
          />
          <PasswordWPB id="password" title="Password" required={true} />
          <Password
            id="password_confirmation"
            title="Confirm Password"
            required={true}
          />

          <div
            className="form-group gap-2 text-right"
            style={{ width: "100%" }}
          >
            <Button
              size="sm"
              type="submit"
              className="btn btn-success float-right"
              style={{ marginTop: "25px" }}
            >
              Process
            </Button>
          </div>
        </Form>
      </ErrorBoundary>
    </FormProvider>
  );
};

export default EditPasswordForm
