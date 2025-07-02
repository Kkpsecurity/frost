import React, { useEffect, useRef, useState } from "react";
import { ClassDataShape, StudentType } from "../../Config/types";
import PageLoader from "../../Components/Widgets/PageLoader";
import { HandelFileUpload } from "./useClassRoomDataHooks";
import { EImageType, compress, compressAccurately } from "image-conversion";

const usePhotoUploaded = ({
    data,
    student,
    photoType,
    debug = false,
}: {
    data: ClassDataShape;
    student: StudentType;
    photoType: string;
    debug?: boolean;
}) => {
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [isUploading, setIsUploading] = useState<boolean>(false);

    const [dimensions, setDimensions] = useState(
        window.innerWidth > 768
            ? { width: 400, height: 300 }
            : { width: 320, height: 280 }
    );

    useEffect(() => {
        const handleResize = () => {
            setDimensions(
                window.innerWidth > 768
                    ? { width: 400, height: 300 }
                    : { width: 320, height: 280 }
            );
        };

        // Attach the event listener
        window.addEventListener("resize", handleResize);

        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, []);

    /**
     * The selected file
     */
    const [selectedFile, setSelectedFile] = useState<File>(null);

    useEffect(() => {
        let objectURL: string;
        if (selectedFile) {
            objectURL = URL.createObjectURL(selectedFile);
        }
        return () => {
            if (objectURL) {
                URL.revokeObjectURL(objectURL);
            }
        };
    }, [selectedFile]);

    /**
     * Reference to the file input element
     */
    const fileInputRef = useRef<HTMLInputElement>(null);

    /**
     * Handle the file change event
     * @param e
     */
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files.length > 0) {
            const file = e.target.files[0];

            // Check file type
            const validFileTypes = ["image/png", "image/jpeg", "image/gif"];
            if (!validFileTypes.includes(file.type)) {
                setErrorMessage(
                    "Invalid file type. Please select PNG, JPG, or GIF."
                );
                e.target.value = ""; // Resets the input
                return;
            }

            if (file.size > 4 * 1024 * 1024) {
                setErrorMessage("File size exceeds 4MB limit.");
                e.target.value = ""; // Resets the input
                return;
            }

            // Resizing the image
            const reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result as string;
                img.onload = () => {
                    const elem = document.createElement("canvas");
                    const scaleFactor = 0.5; // Adjust this value to change the size
                    elem.width = img.width * scaleFactor;
                    elem.height = img.height * scaleFactor;
                    const ctx = elem.getContext("2d");
                    ctx.drawImage(img, 0, 0, elem.width, elem.height);

                    ctx.canvas.toBlob(
                        (blob) => {
                            const resizedFile = new File([blob], file.name, {
                                type: file.type,
                                lastModified: Date.now(),
                            });

                            // Setting the resized file as selected
                            setSelectedFile(resizedFile);
                        },
                        file.type,
                        1
                    );
                };
            };

            setSelectedFile(file);
            setErrorMessage("");
        }
    };

    /**
     * Handle the reset button click
     */
    const handleFileReset = () => {
        setSelectedFile(null);
        setErrorMessage("");
    };

    /**
     * Prepare the file upload hook
     */
    const { mutate: uploadFile, isLoading, isError } = HandelFileUpload();

    /**
     * Convert the given image file to PNG using image-conversion
     * @param {File} file - The image file to convert
     * @returns {Promise<File>} - The converted PNG file
     */
    const convertToPng = async (file) => {
        const convertedFile = await compressAccurately(file, {
            size: 200, // Specify the desired size in kilobytes
            type: EImageType.PNG, // Convert to PNG format
        }); 

        return new File([convertedFile], file.name, { type: "image/png" });
    };

    /**
     * Handle the file upload
     */
    const handleUploadImage = async () => {
        if (!selectedFile) {
            console.error("No file selected");
            return;
        }

        setIsUploading(true);

        // check if student object and its properties are not null or undefined before proceeding
        if (!student || !data.student_unit_id || !student.course_auth_id) {
            console.error("Student data is incomplete", student);
            return;
        }

        try {
            if (selectedFile) {
                // Convert the file to PNG before uploading
                const convertedFile = await convertToPng(selectedFile);

                // Upload the converted file to the server
                const formData = new FormData();
                formData.append("photoType", photoType);
                formData.append(
                    "student_unit_id",
                    data.student_unit_id.toString()
                );                
                formData.append(
                    "course_auth_id",
                    student.course_auth_id.toString()
                );
                formData.append("file", convertedFile);

                uploadFile(formData);

                setTimeout(() => {
                    setIsUploading(false);
                    setSelectedFile(null);
                }, 15000);
            }
        } catch (error) {
            throw new Error("Validation Error: " + error.response.data.message);
        }
    };

    return {
        errorMessage,
        setErrorMessage,
        dimensions,
        selectedFile,
        setSelectedFile,
        fileInputRef,
        handleFileChange,
        handleFileReset,
        handleUploadImage,
        isLoading,
        isUploading,
        isError,
    };
};

export default usePhotoUploaded;
