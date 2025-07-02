import React, { FC, useEffect, useId, useRef, useState } from "react";
import { Button, Card } from "react-bootstrap";
import { ClassDataShape, StudentType } from "../../../../../Config/types";
import { HandelFileUpload } from "../../../../../Hooks/Web/useClassRoomDataHooks";
import { toast } from "react-toastify";
import styled from "styled-components";
import Dropzone from "../../../../../Components/FormElements/Dropzone";
import {
    StyledCard,
    StyledImage,
    StyledDropArea,
} from "../../../../../Styles/ImageUpload.styled";
import usePhotoUploaded from "../../../../../Hooks/Web/usePhotoUploaded";
import PageLoader from "../../../../../Components/Widgets/PageLoader";
import Loader from "../../../../../Components/Widgets/Loader";

/**
 * ImageIDUpload Lets the user upload an image from the local file system
 * features:
 * - Upload an image to the local file system
 * - Reset the image to upload another image
 */

interface ImageIDUploadProps {
    data: ClassDataShape | null;
    student: StudentType;
    photoType: string;
    headshot: string | null;
    idcard: string | null;
    debug: boolean;
}

const ImageIDUpload: FC<ImageIDUploadProps> = ({
    data,
    student,
    photoType,
    headshot,
    idcard,
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
                <Loader />
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
                                            upload. Otherwise, you can proceed
                                            to save your changes.
                                        </div>
                                    </div>
                                )}
                                {isError && <div>Error uploading image</div>}

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
                                            onClick={handleUploadImage}
                                        >
                                            {isLoading
                                                ? "Uploading..."
                                                : "Upload"}
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
