import React from "react";
import { Button, Card, Form, Alert } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import { ErrorBoundary } from "react-error-boundary";

import { ProfileType } from "../../../../Config/types";

/**
 * Import the Form Components
 */
import { AlertFormFallback } from "../../../../ErrorHandeling/AlertFormFallback";
import TextHidden from "../../../../Components/FormElements/TextHidden";
import TextInput from "../../../../Components/FormElements/TextInput";

/**
 * Import Hooks and Validation
 */
import { useUpdateProfile } from "../../../../Hooks/Web/useProfileManager";
import { validateProfile } from "./Validations/profileValidation";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

interface Props {
    profile: ProfileType;
}

const EditProfileForm: React.FC<Props> = ({ profile }) => {

    
    const methods = useForm({
        defaultValues: {
          first_name: profile.fname,
          last_name: profile.lname,
          email: profile.email,
        },
    });

    const {
        mutate: updateProfile,
        isLoading,
        isError,
        error,
    } = useUpdateProfile();

    const updateProfileForm = async (post) => {
        const profile = {
            user_id: post.user_id,
            fname: post.first_name,
            lname: post.last_name,
            email: post.email,
        };

        const isValid = await validateProfile.isValid(profile);
        if (isValid) {
            updateProfile(profile);
            toast.success("Profile Updated Successfully");
        } else {
            console.error("Post Data: ", post);
            toast.error("Profile Update Failed");
        }
    };

    if (isError) {
        return (
            <Alert variant="danger">
                {error instanceof Error ? error.message : ""}
            </Alert>
        );
    }

    return (
        <FormProvider {...methods}>
            <ErrorBoundary FallbackComponent={AlertFormFallback}>
                <Card>
                    <Card.Header>
                        <h3 className="text-dark">Edit Personal Detail</h3>
                    </Card.Header>
                    <Form
                        action="#"
                        method="post"
                        role="form"
                        className="form p-2"
                        onSubmit={methods.handleSubmit(updateProfileForm)}
                    >
                        <ToastContainer />
                        <TextHidden id="user_id" value={profile.id} />

                        <TextInput
                            id="first_name"
                            value={profile.fname}
                            title="First Name"
                            required
                        />

                        <TextInput
                            id="last_name"
                            value={profile.lname}
                            title="Last Name"
                            required
                        />

                        <TextInput
                            id="email"
                            value={profile.email}
                            title="Email Address"
                            required
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
                </Card>
            </ErrorBoundary>
        </FormProvider>
    );
};

export default EditProfileForm;
