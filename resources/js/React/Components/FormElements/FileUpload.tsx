import React from "react";
import { Form, Row, Col, Button } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

type TextProps = {
    title: string;
    onFileSelect: (file: File) => void;
    required?: boolean;
};

const Text: React.FC<TextProps> = ({
    title,
    onFileSelect,
    required = false,
}) => {
    const { register } = useFormContext();
    const [uploadFile, setUploadFile] = React.useState<File | null>(null);
    const [fileName, setFileName] = React.useState<string>("");
    const [imageUrl, setImageUrl] = React.useState<string | null>(null);

    const removeFile = (fileName: string) => {
        // implementation goes here
    };

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files[0];
        setUploadFile(file);
        setFileName(file.name);

        const reader = new FileReader();
        reader.onloadend = () => {
            setImageUrl(reader.result as string);
        };
        reader.readAsDataURL(file);
    };

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor="files">{title}</Form.Label>
                </Col>
                <Col lg={8}>
                    {uploadFile ? (
                        <div>
                            <img src={imageUrl} alt={fileName} />

                            <Button
                                className="fas fa-trash-alt"
                                onClick={() => removeFile(fileName)}
                            />
                        </div>
                    ) : (
                        <Form.Control
                            type="file"
                            name="files"
                            title=""
                            id="files"
                            placeholder={`Select a file for ${title}`}
                            {...register("files", { required: required })}
                            onChange={handleFileChange}
                        />
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Text;
