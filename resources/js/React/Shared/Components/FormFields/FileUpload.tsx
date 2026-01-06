import React from "react";
import { Form, Row, Col, Button } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface FileUploadProps {
    id: string;
    title: string;
    onFileSelect?: (file: File) => void;
    required?: boolean;
}

const FileUpload: React.FC<FileUploadProps> = ({
    id,
    title,
    onFileSelect,
    required = false,
}) => {
    const { register, formState: { errors }, setValue } = useFormContext();
    const [uploadFile, setUploadFile] = React.useState<File | null>(null);
    const [fileName, setFileName] = React.useState<string>("");
    const [imageUrl, setImageUrl] = React.useState<string | null>(null);

    const removeFile = () => {
        setUploadFile(null);
        setFileName("");
        setImageUrl(null);
        setValue(id, null);
        if (onFileSelect) {
            onFileSelect(null as any);
        }
    };

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            setUploadFile(file);
            setFileName(file.name);
            setValue(id, file);

            if (onFileSelect) {
                onFileSelect(file);
            }

            const reader = new FileReader();
            reader.onloadend = () => {
                setImageUrl(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>
                        {title}
                        {required && <span className="text-danger">*</span>}
                    </Form.Label>
                </Col>
                <Col lg={8}>
                    {uploadFile ? (
                        <div className="d-flex align-items-center gap-2">
                            {imageUrl && (
                                <img
                                    src={imageUrl}
                                    alt={fileName}
                                    className="img-thumbnail"
                                    style={{ maxWidth: '100px', maxHeight: '100px' }}
                                />
                            )}
                            <div className="flex-grow-1">
                                <p className="mb-1"><strong>{fileName}</strong></p>
                                <Button
                                    variant="danger"
                                    size="sm"
                                    onClick={removeFile}
                                >
                                    <i className="fas fa-trash-alt"></i> Remove
                                </Button>
                            </div>
                        </div>
                    ) : (
                        <>
                            <Form.Control
                                type="file"
                                name={id}
                                id={id}
                                className={errors[id] ? 'is-invalid' : ''}
                                placeholder={`Select a file for ${title}`}
                                {...register(id, { required: required ? 'This field is required' : false })}
                                onChange={handleFileChange}
                            />
                            {errors[id] && (
                                <div className="invalid-feedback">
                                    <i className="fa fa-exclamation"></i>
                                    {' '}
                                    {errors[id]?.message?.toString() || `${title} is required`}
                                </div>
                            )}
                        </>
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default FileUpload;
