import { useState } from "react";

export const useValidateModalData = () => {
    const [show, setShow] = useState<boolean>(false);
    const [showDecline, setShowDecline] = useState<boolean>(false);

    const [validateType, setValidateType] = useState<string | null>(null);

    // Sets the Modal State
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);
    const handleDeclineClose = () => setShowDecline(false);
    const handleDeclineShow = () => setShowDecline(true);

    return {
        show,
        setShow,
        showDecline,
        setShowDecline,
        validateType,
        handleClose,
        handleShow,
        handleDeclineClose,
        handleDeclineShow,
    };
};

