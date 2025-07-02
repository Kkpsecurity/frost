import React, { useState } from 'react';
import Modal from 'react-bootstrap/Modal';
import Button from 'react-bootstrap/Button';
import useEffect from 'react';

type userCallType = {
    id: number;
    fname: string;
    lname: string;
    email: string;
    isInstructor: boolean;
};

interface NotificationsProps {
    isCallReceived: boolean;
    setIsCallReceived: (isCallReceived: boolean) => void;
    receiver: userCallType;
    receiver_id: number;
    AnswerCall: () => void;
    caller: userCallType;
    notificationMessage: string;
}

const Notifications: React.FC<NotificationsProps> = ({
    isCallReceived,
    setIsCallReceived,
    receiver_id,
    receiver,
    AnswerCall,
    caller,
    notificationMessage,
}) => {

    const [showModal, setShowModal] = useState(false);

    const handleClose = () => setShowModal(false);
    const handleShow = () => setShowModal(true);
    const handleAccept = () => {
        setIsCallReceived(true);
    };

    React.useEffect(() => {
        if (isCallReceived === true) {
            if(receiver.id === receiver_id) handleShow();
        }

        return () => {
            handleClose();
        }
    }, [isCallReceived]);

  return (
    <>
      <Modal show={showModal} onHide={handleClose}>
        <Modal.Header closeButton>
          <Modal.Title>Incoming Call</Modal.Title>
        </Modal.Header>
        <Modal.Body>{notificationMessage}</Modal.Body>
        <Modal.Footer>
          <Button variant="secondary" onClick={handleClose}>
            Decline
          </Button>
          <Button variant="primary" onClick={handleAccept}>
            Accept
          </Button>
        </Modal.Footer>
      </Modal>
    </>
  );
}



export default Notifications
