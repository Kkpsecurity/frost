import React, { useState } from "react";
import { Form, FormGroup, FormText, Button, Col, Row } from "react-bootstrap";

import { FormProvider, useForm, SubmitHandler } from "react-hook-form";
import GravatarInput from "../../../../Components/FormElements/GravatarInput";
import FileUpload from "../../../../Components/FormElements/FileUpload";
import AccountAvatar from "../../../../Components/Widgets/AccountAvatar";

interface EditAvatarFormProps {
    avatar: string;
    debug: boolean;
}

const EditAvatarForm: React.FC<EditAvatarFormProps> = ({
    avatar,
    debug = false,
}) => {
    const [useGravatar, setUseGravatar] = useState(false);
    const [previewUrl, setPreviewUrl] = useState(null);
    const [file, setFile] = useState(null);

    const methods = useForm();

    const handleFileChange = (event) => {
        const selectedFile = event.target.files[0];
        setFile(selectedFile);

        // Show a preview of the selected image
        const reader = new FileReader();
        reader.onloadend = () => {
            setPreviewUrl(reader.result);
        };
        reader.readAsDataURL(selectedFile);
    };

    const handleGravatarChange = (event) => {
        setUseGravatar(event.target.checked);
    };

    const updatePassword = (data) => {};

    const onFileSelect = () => {};

    return (
        <FormProvider {...methods}>
            <Form
                onSubmit={methods.handleSubmit(updatePassword)}
                style={{ width: "100%", background: "#efefef" }}
            >
                <GravatarInput debug={debug} />
                <Row>
                    <Col lg={4}>
                        <AccountAvatar
                            avatar={avatar}
                            width={120}
                            debug={debug}
                        />
                    </Col>
                    <Col lg={8}>
                        <FileUpload
                            title="Avatar Upload"
                            onFileSelect={onFileSelect}
                            required={false}
                        />
                        <Button type="submit">Upload Avatar</Button>
                    </Col>
                </Row>
            </Form>
        </FormProvider>
    );
};

export default EditAvatarForm;
