import React, { useEffect, useRef, useState } from "react";
import { StudentType } from "../../../types/students.types";
import PageLoader from "../../Components/Widgets/PageLoader";
import { EImageType, compress, compressAccurately } from "image-conversion";
import apiClient from "../../../../../../Config/axios";

// Define ClassDataShape locally since it might not be available
interface ClassDataShape {
    course_auth_id?: number;
    course_id?: number;
    [key: string]: any;
}

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
     * Prepare the file upload function using fetch
     */
    const [isLoading, setIsLoading] = useState(false);
    const [isError, setIsError] = useState(false);

    /**
     * Upload file to server using apiClient (with interceptors)
     */
    const uploadFileToServer = async (formData: FormData) => {
        setIsLoading(true);
        setIsError(false);

        try {
            const response = await apiClient.post('/classroom/upload-student-photo', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            });

            console.log('✅ Upload successful:', response.data);
            setIsLoading(false);
            return response.data;
        } catch (error) {
            console.error('❌ Upload failed:', error);
            setIsError(true);
            setErrorMessage(error.response?.data?.message || error.message || 'Upload failed');
            setIsLoading(false);
            throw error;
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
     * Handle the file upload - modified for captured images from canvas
     */
    const handleUploadCapturedImage = async (capturedBlob: Blob, filename: string) => {
        if (!capturedBlob) {
            console.error("No captured blob provided");
            return;
        }

        setIsUploading(true);

        // check if required student data is available before proceeding
        if (!student) {
            console.error("Student object is null or undefined", student);
            setErrorMessage("Student data not available. Please refresh the page.");
            setIsUploading(false);
            return;
        }

        // Get course_auth_id from multiple possible sources (now optional)
        const course_auth_id = student.course_auth_id ||
                              data?.course_auth_id ||
                              student.course_id ||
                              data?.course_id ||
                              // Try to get from course_auths array (first active one)
                              student.course_auths?.[0]?.id ||
                              // Try localStorage as fallback
                              (() => {
                                  try {
                                      const saved = localStorage.getItem('frost_selected_course_auth_id');
                                      return saved ? parseInt(saved) : null;
                                  } catch (error) {
                                      console.warn('Could not read course_auth_id from localStorage:', error);
                                      return null;
                                  }
                              })() ||
                              // Try reading from DOM props as final fallback
                              (() => {
                                  try {
                                      const propsElement = document.getElementById("student-props");
                                      if (propsElement) {
                                          const props = JSON.parse(propsElement.textContent || "{}");
                                          return props.course_auth_id || props.selected_course_auth_id || null;
                                      }
                                  } catch (error) {
                                      console.warn('Could not read course_auth_id from DOM props:', error);
                                  }
                                  return null;
                              })();

        // Get course_date_id if available
        const course_date_id = data?.course_date_id ||
                              data?.id ||
                              (() => {
                                  try {
                                      const propsElement = document.getElementById("student-props");
                                      if (propsElement) {
                                          const props = JSON.parse(propsElement.textContent || "{}");
                                          return props.course_date_id || null;
                                      }
                                  } catch (error) {
                                      return null;
                                  }
                                  return null;
                              })();

        if (!student.id) {
            console.error("Required student ID missing:", {
                student_id: student.id,
                student,
                data
            });
            setErrorMessage("Required student ID missing. Please refresh the page.");
            setIsUploading(false);
            return;
        }

        // Log what we're sending (course_auth_id and course_date_id are now optional)
        console.log("Upload attempt with IDs:", {
            course_auth_id,
            course_date_id,
            student_id: student.id,
            photoType
        });

        try {
            // Convert blob to File
            const file = new File([capturedBlob], filename, { type: "image/jpeg" });

            // Convert the file to PNG before uploading
            const convertedFile = await convertToPng(file);

            // Upload the converted file to the server
            const formData = new FormData();
            formData.append("photoType", photoType);
            if (course_auth_id) formData.append("course_auth_id", course_auth_id.toString());
            if (course_date_id) formData.append("course_date_id", course_date_id.toString());
            formData.append("student_id", student.id.toString()); // Use student.id instead of student_unit_id
            formData.append("file", convertedFile);

            // Use the fetch-based upload
            await uploadFileToServer(formData);

            console.log('✅ Image uploaded successfully');
            setIsUploading(false);

        } catch (error) {
            console.error("Upload error:", error);
            setErrorMessage("Upload failed: " + error.message);
            setIsUploading(false);
        }
    };

    /**
     * Handle the file upload - original version for file input
     */
    const handleUploadImage = async () => {
        if (!selectedFile) {
            console.error("No file selected");
            return;
        }

        setIsUploading(true);

        // check if required student data is available before proceeding
        if (!student) {
            console.error("Student object is null or undefined", student);
            setErrorMessage("Student data not available. Please refresh the page.");
            setIsUploading(false);
            return;
        }

        // Get course_auth_id from multiple possible sources (now optional)
        const course_auth_id = student.course_auth_id ||
                              data?.course_auth_id ||
                              student.course_id ||
                              data?.course_id ||
                              // Try to get from course_auths array (first active one)
                              student.course_auths?.[0]?.id ||
                              // Try localStorage as fallback
                              (() => {
                                  try {
                                      const saved = localStorage.getItem('frost_selected_course_auth_id');
                                      return saved ? parseInt(saved) : null;
                                  } catch (error) {
                                      console.warn('Could not read course_auth_id from localStorage:', error);
                                      return null;
                                  }
                              })() ||
                              // Try reading from DOM props as final fallback
                              (() => {
                                  try {
                                      const propsElement = document.getElementById("student-props");
                                      if (propsElement) {
                                          const props = JSON.parse(propsElement.textContent || "{}");
                                          return props.course_auth_id || props.selected_course_auth_id || null;
                                      }
                                  } catch (error) {
                                      console.warn('Could not read course_auth_id from DOM props:', error);
                                  }
                                  return null;
                              })();

        // Get course_date_id if available
        const course_date_id = data?.course_date_id ||
                              data?.id ||
                              (() => {
                                  try {
                                      const propsElement = document.getElementById("student-props");
                                      if (propsElement) {
                                          const props = JSON.parse(propsElement.textContent || "{}");
                                          return props.course_date_id || null;
                                      }
                                  } catch (error) {
                                      return null;
                                  }
                                  return null;
                              })();

        if (!student.id) {
            console.error("Required student ID missing:", {
                student_id: student.id,
                student,
                data
            });
            setErrorMessage("Required student ID missing. Please refresh the page.");
            setIsUploading(false);
            return;
        }

        // Log what we're sending (course_auth_id and course_date_id are now optional)
        console.log("File upload attempt with IDs:", {
            course_auth_id,
            course_date_id,
            student_id: student.id,
            photoType
        });

        try {
            // Convert the file to PNG before uploading
            const convertedFile = await convertToPng(selectedFile);

            // Upload the converted file to the server
            const formData = new FormData();
            formData.append("photoType", photoType);
            if (course_auth_id) formData.append("course_auth_id", course_auth_id.toString());
            if (course_date_id) formData.append("course_date_id", course_date_id.toString());
            formData.append("student_id", student.id.toString()); // Use student.id instead of student_unit_id
            formData.append("file", convertedFile);

            // Use the fetch-based upload
            await uploadFileToServer(formData);

            console.log('✅ File uploaded successfully');
            setIsUploading(false);
            setSelectedFile(null);
        } catch (error) {
            console.error("Upload error:", error);
            setErrorMessage("Upload failed: " + error.message);
            setIsUploading(false);
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
        handleUploadCapturedImage,
        isLoading,
        isUploading,
        isError,
    };
};

export default usePhotoUploaded;
