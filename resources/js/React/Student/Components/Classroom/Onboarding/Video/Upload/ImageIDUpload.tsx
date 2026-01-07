import React, { FC, useEffect, useId, useRef, useState } from "react";
import { Button, Card } from "react-bootstrap";
import { toast } from "react-toastify";
import styled from "styled-components";
import Dropzone from "../../../../../../Shared/Components/FormFields/Dropzone";
import { StyledCard, StyledImage, StyledDropArea } from "../../../../../../Styles";
import usePhotoUploaded from "../../../../../../Hooks/Web/usePhotoUploaded";
import { StudentType } from "@/React/Student/types/students.types";
/**
 * ImageIDUpload Lets the user upload an image from the local file system
 * features:
 * - Upload an image to the local file system
 * - Reset the image to upload another image
 */

interface ImageIDUploadProps {
    data: any | null;
    student: StudentType;
    photoType: string;
    headshot: string | null;
    idcard: string | null;
    onStepComplete?: () => void;
    debug: boolean;
}

const ImageIDUpload: FC<ImageIDUploadProps> = ({
    data,
    student,
    photoType,
    headshot,
    idcard,
    onStepComplete,
    debug = false,
}) => {
    const {
        errorMessage,
        setErrorMessage,
        dimensions,
        selectedFile,
        setSelectedFile,
        fileInputRef,
        handleFileChange,
        handleFileReset,
        handleUploadImage,
        isUploading,
        isLoading,
        isError,
    } = usePhotoUploaded({ data, student, photoType, debug });

    if (isUploading)
        return (
            <div className="loading">
                <div className="d-flex justify-content-center align-items-center" style={{ minHeight: "200px" }}>
                    <div className="spinner-border" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        );

    return (
        <>
            <div>
                {errorMessage && (
                    <div className="alert alert-danger">{errorMessage}</div>
                )}

                {!selectedFile ? (
                    <Dropzone
                        dimensions={dimensions}
                        fileInputRef={fileInputRef}
                        handleFileChange={handleFileChange}
                        StyledDropArea={StyledDropArea}
                    />
                ) : (
                    <>
                        {selectedFile && (
                            <>
                                {!isError && (
                                    <div className="alert alert-success">
                                        Select Image:
                                        <div className="mt-2">
                                            If you're not satisfied with the
                                            image, you can always retry the
                                            upload. Otherwise, you can proceed to
                                            save your changes.
                                        </div>
                                    </div>
                                )}

                                {isError && (
                                    <div>Error uploading image</div>
                                )}

                                <StyledCard>
                                    <StyledImage
                                        src={URL.createObjectURL(selectedFile)}
                                    />
                                    <Card.Footer className="d-flex justify-content-between">
                                        <Button
                                            onClick={handleFileReset}
                                            variant="danger"
                                        >
                                            Reset
                                        </Button>
                                        <button
                                            className="btn btn-sm btn-success float-end"
                                            onClick={async () => {
                                                try {
                                                    await handleUploadImage();
                                                    onStepComplete?.();
                                                } catch (error) {
                                                    // Error state is handled inside the hook
                                                }
                                            }}
                                        >
                                            {isLoading ? "Uploading..." : "Upload"}
                                        </button>
                                    </Card.Footer>
                                </StyledCard>
                            </>
                        )}
                    </>
                )}
            </div>
        </>
    );
};

export default ImageIDUpload;
