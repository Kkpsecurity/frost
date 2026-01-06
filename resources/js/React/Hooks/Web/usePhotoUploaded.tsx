import React, { useEffect, useRef, useState } from "react";
import { ClassDataShape, StudentType } from "../../Config/types";
import PageLoader from "@/React/Components/Widgets/PageLoader";
import { EImageType, compress, compressAccurately } from "image-conversion";

const usePhotoUploaded = ({
    data,
    student,
    photoType,
    debug = false,
}: {
    data: ClassDataShape | null;
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
     * Upload state
     */
    const [isLoading, setIsLoading] = useState(false);
    const [isError, setIsError] = useState(false);

    /**
     * Simple file upload function
     */
    const uploadFile = async (formData: FormData) => {
        setIsLoading(true);
        setIsError(false);
        try {
            const response = await fetch('/api/upload-photo', {
                method: 'POST',
                body: formData,
            });

            if (!response.ok) {
                throw new Error('Upload failed');
            }

            return await response.json();
        } catch (error) {
            setIsError(true);
            throw error;
        } finally {
            setIsLoading(false);
        }
    };

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

        // Enhanced validation with detailed logging
        console.log('🔍 Validating upload data:');
        console.log('  - student:', student);
        console.log('  - data:', data);
        console.log('  - selectedFile:', selectedFile);

        // Check if data object exists first, try to construct from student if missing
        if (!data) {
            console.warn("⚠️ Data object is null, attempting to construct from student object");

            // Try to find student_unit_id from student.student_units if available
            if (student?.student_units && student.student_units.length > 0) {
                console.log('📋 Found student_units, using first available:', student.student_units[0]);
                // Use the first student unit as fallback
                const fallbackData = {
                    student_unit_id: student.student_units[0].id
                };
                console.log('🔧 Created fallback data:', fallbackData);

                // Continue with fallback data - modify the rest of the function to use fallbackData
            } else {
                console.error("❌ Data object is null and no student_units available for fallback");
                console.error("❌ Available student properties:", Object.keys(student || {}));
                alert('Error: Student enrollment data is not available. Please ensure you are properly enrolled in this course and try again.');
                setIsUploading(false);
                return;
            }
        }

        // Check if student object and its properties are not null or undefined before proceeding
        if (!student) {
            console.error("❌ Student object is null or undefined");
            alert('Error: Student information is not available. Please refresh the page and try again.');
            setIsUploading(false);
            return;
        }

        // Determine the student_unit_id to use (from data or fallback)
        let studentUnitId;
        if (data && data.student_unit_id) {
            studentUnitId = data.student_unit_id;
            console.log('✅ Using student_unit_id from data:', studentUnitId);
        } else if (student?.student_units && student.student_units.length > 0) {
            studentUnitId = student.student_units[0].id;
            console.log('🔧 Using fallback student_unit_id from student.student_units:', studentUnitId);
        } else {
            console.error("❌ student_unit_id not available in data or student.student_units");
            console.error("❌ data:", data);
            console.error("❌ student.student_units:", student?.student_units);
            alert('Error: Student enrollment ID is missing. Please ensure you are properly enrolled and try again.');
            setIsUploading(false);
            return;
        }

        if (!student.course_auth_id) {
            console.error("❌ course_auth_id is missing from student object", student);
            alert('Error: Course authorization ID is missing. Please refresh the page and try again.');
            setIsUploading(false);
            return;
        }

        console.log('✅ All validation checks passed');

        try {
            if (selectedFile) {
                // Convert the file to PNG before uploading
                const convertedFile = await convertToPng(selectedFile);

                // Upload the converted file to the server
                const formData = new FormData();
                formData.append("photoType", photoType);
                formData.append(
                    "student_unit_id",
                    studentUnitId.toString()
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
            console.error("❌ Upload error:", error);
            console.error("Error details:", {
                message: error?.message,
                response: error?.response?.data,
                student: student,
                data: data,
                selectedFile: selectedFile
            });

            setIsUploading(false);

            // Show user-friendly error message
            const errorMessage = error?.response?.data?.message || error?.message || 'Unknown upload error';
            alert(`Upload failed: ${errorMessage}. Please try again.`);
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
