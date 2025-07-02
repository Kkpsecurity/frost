import React, { useState, useEffect } from "react";
import { MessageConsoleType } from "../../Config/types";
import { useDeletePhoto, validateStudentHook } from "./useInstructorHooks";
import apiClient from "../../Config/axios";

const useValidationHook = ({ classData, student, studentAuthId }) => {
    const defaultImage = "no-image.jpg";
    const [loading, setLoading] = useState<boolean>(false);
    const [showPreview, setShowPreview] = useState<boolean>(false);
    const [validationMessage, setValidationMessage] =
        useState<MessageConsoleType | null>(null);
    const [validationStep, setValidationStep] = useState<
        "begin" | "idcard" | "headshot" | "completed"
    >("idcard");
    const [validationMode, setValidationMode] = useState<
        "validate" | "decline" | "pending"
    >("pending");
    const [idCardStatus, setIdCardStatus] = useState<boolean | null>(null);
    const [headShotStatus, setHeadShotStatus] = useState<boolean | null>(null);

    /**
     * Prepare Mutation Hooks
     */
    const { mutate: validateStudentSession } = validateStudentHook();
    const { mutate: deleteImage } = useDeletePhoto();

    /**
     * Validation Types
     */
    const validateTypes = [
        { value: "", text: "Select License Type" },
        { value: "Drivers License", text: "Drivers License" },
        { value: "State Issued ID", text: "State Issued ID" },
        { value: "Military / Govt ID", text: "Military / Govt ID" },
        { value: "Passport", text: "Passport" },
        { value: "Personal Recognition", text: "Personal Recognition" },
        { value: "Previously Verified", text: "Previously Verified" },
        { value: "Other", text: "Other" },
    ];

    /**
     * Headshot Types
     */
    const headshotTypes = [
        { value: "", text: "Select Headshot Reason" },
        { value: "blurry", text: "The Image Is Too Blurry" },
        { value: "does-not-match", text: "The Image Does Not Match the ID" },
    ];

    /**
     * Decline Types
     */
    const declineTypes = [
        { value: "", text: "Select Decline Reason" },
        { value: "No ID", text: "No ID" },
        { value: "No Headshot", text: "No Headshot" },
        { value: "ID not clear", text: "ID not clear" },
        { value: "Headshot not clear", text: "Headshot not clear" },
        { value: "ID expired", text: "ID expired" },
        {
            value: "Headshot doesn't match ID",
            text: "Headshot doesn't match ID",
        },
        { value: "Other", text: "Other" },
    ];

    const handleClose = () => {
        setShowPreview(false);
    };

    const getValidationTypes = () => validateTypes;

    const getDeclineTypes = () => declineTypes;

    const getHeadshotTypes = () => headshotTypes;

    const handleDeletePhoto = () => {
        deleteImage(ImageData);
    };

    const validateImage = (image: string): boolean => {
        const filename = image?.split("/").pop();
        return filename && filename !== defaultImage;
    };

    const validateHeadshotRequest = async (): Promise<boolean> => {
        const isMissing = validateImage(student?.validations?.headshot);
        if (!isMissing) {
            setValidationMessage({
                status: "danger",
                message: "Headshot not Uploaded.",
            });
            return false;
        }
        return true;
    };

    const validateIDRequest = async (): Promise<boolean> => {
        const isMissing = validateImage(student?.validations?.idcard);
        if (!isMissing) {
            setValidationMessage({
                status: "danger",
                message: "IdCard not Uploaded.",
            });
            return false;
        }
        return true;
    };

    const handlePhotoValidation = async (data: FormData) => {
        const headshotValid = await validateHeadshotRequest();
        const idValid = await validateIDRequest();

        console.log("Headshot Valid: ", headshotValid, "ID Valid: ", idValid);

    };

    // if (headshotValid) {
    //     setValidationMessage({
    //         status: "success",
    //         message: "Headshot has been successfully validated.",
    //     });
    // }

    // if (idValid) {
    //     setValidationMessage({
    //         status: "success",
    //         message: "ID card has been successfully validated.",
    //     });
    // }

    // // API call to send validation info to the server
    // try {
    //     const response = await apiClient('support/validate/student', {
    //         method: 'POST',
    //         data: data,               
    //     });

    //     if (!response.data.success) {
    //         throw new Error('Failed to validate photo');
    //     }

    //     const result = await response.data.json();
    //     console.log('Server Response:', result);
    //     setValidationMessage({
    //         status: "success",
    //         message: "Photo validation data has been sent to the server.",
    //     });
    // } catch (error) {
    //     console.error('Error:', error);
    //     setValidationMessage({
    //         status: "danger",
    //         message: "Failed to send validation data to the server." + error.message,
    //     });
    // }


    /**
     * useEffect hook to set initial validation step
     */
    useEffect(() => {
        setLoading(true);
        let initialStep: "begin" | "idcard" | "headshot" | "completed" =
            "begin";

        const isHeadshotDefault = student.validations.headshot
            ? student.validations.headshot.includes(defaultImage)
            : true; 

        const isIdcardDefault = student.validations.idcard
            ? student.validations.idcard.includes(defaultImage)
            : true; 

        if (initialStep === "begin") {
            // make sure we have the student and validation data befor moving to next step
            setTimeout(() => {
                setValidationMessage({
                    status: "default",
                    message: "Preparing ID Validation",
                });

                if (isIdcardDefault && isHeadshotDefault) {
                    setValidationMessage({
                        status: "default",
                        message: "Validate Headshot and ID Card.",
                    });

                    initialStep = "headshot";
                }

                setValidationStep(initialStep);
            }, 2000);
        } else if (!isHeadshotDefault) {
            setTimeout(() => {
                initialStep =
                    student.validations.headshot_status === 1
                        ? "completed"
                        : "headshot";

                setValidationStep(initialStep);
            }, 2000);
        } else if (!isIdcardDefault) {
            setTimeout(() => {
                initialStep =
                    student.validations.idcard_status === 1
                        ? "completed"
                        : "idcard";
                setValidationStep(initialStep);
            }, 2000);
        }

        setLoading(false);
    }, [student]);

    return {
        loading,
        showPreview,
        idCardStatus,
        headShotStatus,
        validationStep,
        validationMode,
        validationMessage,
        handleClose,
        getDeclineTypes,
        getHeadshotTypes,
        setValidationMode,
        setValidationStep,
        handleDeletePhoto,
        getValidationTypes,
        setValidationMessage,
        handlePhotoValidation,
    };
};

export default useValidationHook;

// const handleDeletePhoto = (type: string) => {
//     const fileData = {
//         validation_path: type === "headshot" ? headshot : idcard,
//         file: type === "headshot" ? headshot : idcard,
//         fileType: type,
//         id: type,
//     };

//     deleteImage(fileData);

//     if (type === "headshot") {
//         setHeadshot(defaultImage);
//     } else if (type === "idcard") {
//         const idcardFile = defaultImage;
//         setIdcard(idcardFile);
//     }
// };

// const handleValidate = async (data) => {
//     let isError = false;
//     console.log("Validation Data: ", data);

//     if (data.headshot_delete) {
//         handleDeletePhoto(data.imagetype);
//     }

//     if (data.idcard_delete) {
//         handleDeletePhoto(data.imagetype);
//     }

//     const validationSchema = yup.object().shape({
//         course_date_id: yup.number().required(), // Course Date ID
//         course_auth_id: yup.string().required(), // Student Auth ID
//         instructor_id: yup.number().required(), // Instructor ID
//         course_id: yup.string().required(), // Course ID
//         validate_type:
//             data.imagetype === "idcard"
//                 ? yup.string().required()
//                 : yup.string(),
//         message:
//             validationMode === "decline" // Is the validation mode declined?
//                 ? yup
//                       .string()
//                       .required(
//                           "Message is required when validation is declined."
//                       )
//                 : yup.string(),
//     });

//     const formatted = {
//         course_date_id: parseInt(data.course_date_id),
//         course_auth_id: String(studentAuthId),
//         type: data.imagetype, // "headshot" or "idcard"
//         validation_action: validationMode, // "validate" or "decline"
//         instructor_id: ClassData.instructor.id,
//         course_id: String(ClassData.course.id),
//         message: data?.message,
//         validate_type: data?.validate_type, // Validation types
//     };

//     // console.log("PostData: ", data);

//     try {
//         await validationSchema.validate(formatted);

//         const response = validateStudentSession(formatted);
//         console.log("Validation Response: ", response);
//         setMessage({
//             status: "success",
//             message: "Student has been validated.",
//         });
//     } catch (error) {
//         const errorMessage = error.message || "An error occurred";
//         setMessage({
//             status: "danger",
//             message: errorMessage,
//         });
//         isError = true;
//     }

//     if (!isError) {
//         setTimeout(() => {
//             setShow(false);
//         }, 3000);
//     }
// };

// useEffect(() => {
//     let initialStep: "idcard" | "headshot" | "completed" = "idcard"; // Default step

//     const isHeadshotDefault =
//         student.validations.headshot === "no-image.jpg";
//     const isIdcardDefault = student.validations.idcard === "no-image.jpg";

//     // If the headshot is not the default image, check its validation status
//     if (!isHeadshotDefault) {
//         switch (student.validations.headshot_status) {
//             case 1: // Headshot validated
//                 initialStep = "completed";
//                 break;
//             case -1: // Headshot not validated
//             case 0: // Headshot not selected/validated yet
//             default:
//                 initialStep = "headshot";
//                 break;
//         }
//     }

//     // If the ID card is not the default image, but the headshot is or hasn't been validated yet, check ID card's status
//     if (!isIdcardDefault && initialStep !== "completed") {
//         switch (student.validations.idcard_status) {
//             case 1: // ID card validated
//                 initialStep = "headshot";
//                 break;
//             case -1: // ID card not validated
//             case 0: // ID card not selected/validated yet
//             default:
//                 initialStep = "idcard";
//                 break;
//         }
//     }

//     // If both are default images, start with ID card validation
//     if (isHeadshotDefault && isIdcardDefault) {
//         initialStep = "idcard";
//     }

//     // Now set the initial validation step
//     setValidationStep(initialStep);
// }, [student]);

// return {
//     show,
//     message,
//     handleClose,
//     idCardStatus,
//     headshotTypes,
//     validationMode,
//     handleValidate,
//     missingPhotos,
//     validationStep,
//     headShotStatus,
//     getDeclineTypes,
//     getHeadshotTypes,
//     handleDeletePhoto,
//     setValidationMode,
//     setValidationStep,
//     getValidationTypes,
// };

// useEffect(() => {
//     const schema = yup.object().shape({
//         classData: yup.object().required("Class data is required"),
//         student: yup.object().required("Student data is required"),
//         studentAuthId: yup.string().required("Student auth ID is required"),
//     });

//     schema
//         .validate({ classData, student, studentAuthId })
//         .then(() => {
//             // Prop validation succeeded
//             console.log("All required props are provided.");
//         })
//         .catch((error) => {
//             // Handle missing props or validation errors
//             console.error("Validation error:", error.message);
//         });
// }, [classData, student, studentAuthId]);

// const [headshot, setHeadshot] = useState<string | null>(null);
// const [idcard, setIdcard] = useState<string | null>(null);
// const [showPreview, setShowPreview] = useState<boolean>(false);

// /**
//  * Set the Mode of Validations
//  */
// const [validationMode, setValidationMode] = useState<
//     "validate" | "decline"
// >("validate");

// /**
//  * Detect which step of the validation weather we are on the headshot or idcard
//  */
// const [validationStep, setValidationStep] = useState<
//     "idcard" | "headshot" | "completed"
// >("idcard");

// const handleClose = () => {
//     setShowPreview(false);
// };

// const getValidationTypes = () => {
//     return validateTypes;
// };

// const getDeclineTypes = () => {
//     return declineTypes;
// };

// const getHeadshotTypes = () => {
//     return headshotTypes;
// };

// /**
//  * Check if the student has a headshot or idcard validated
//  */
// const [idCardStatus, setIdCardStatus] = useState<boolean | null>(null);
// const [headShotStatus, setHeadShotStatus] = useState<boolean | null>(null);

// /**
//  * Photo Upload Messages
//  */
// const [validationMessage, setValidationMessage] =
//     useState<MessageConsoleType | null>(null);

// /**
//  * Check to see if the image has the default image
//  */
// const validateHeadshot = (headshot: string): boolean => {
//     const filename = headshot?.split("/").pop();
//     if (!filename || filename === defaultImage) {
//         return false;
//     }

//     return true;
// };

// const validateID = (idcard: string): boolean => {
//     const filename = idcard?.split("/").pop();
//      if (!filename || filename === defaultImage) {
//         return false;
//     }

//     return true;
// };

// /**
//  * Validate the student's headshot.
//  */
// const validateHeadshotRequest = async (): Promise<boolean> => {
//     // Return the result of the headshot validation
//     const isMissing = validateHeadshot(headshot || "");
//     // Simulate network request delay
//     if (isMissing) {
//         setValidationMessage({
//             status: "danger",
//             message: `Headshot not Uploaded.`,
//         });

//         return true;
//     }

//     return false;
// };

// /**
//  * validate the ID card.
//  */
// const validateIDRequest = async (): Promise<boolean> => {
//     // Return the result of the headshot validation
//     const isMissing = validateID(idcard || "");
//     // Simulate network request delay
//     if (isMissing) {
//         setValidationMessage({
//             status: "danger",
//             message: `IdCard not Uploaded.`,
//         });

//         return false;
//     }

//     return true;
// };

// /**
//  * Handles the sequential photo validation process.
//  * form data is the data from the form hooks
//  */
// const handlePhotoValidation = async (data) => {
//     // Validate headshot with simulated request
//     const missingHeadshot = await validateHeadshotRequest();
//     if (missingHeadshot) {
//         // If both validations pass
//         setValidationMessage({
//             status: "success",
//             message:
//                 "Headshot card have been successfully validated.",
//         });
//     }

//     // Validate ID with simulated request
//     const missingID = await validateIDRequest();
//     if ( missingID) {
//         // If both validations pass
//         setValidationMessage({
//             status: "success",
//             message:
//                 "ID card have been successfully validated.",
//         });
//     }
// };

// /**
//  * Declined Photos
//  */
// const validateDeclineIDVerfication = () => {};

// const validateIDVerification = () => {};

// const handleDeletePhoto = () => {};
