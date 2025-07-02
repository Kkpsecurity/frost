import React, { useState } from 'react';
import Modal from 'react-bootstrap/Modal';
import Button from 'react-bootstrap/Button';

const Receiver = ({ peerId, createAnswer }) => {
  const [showModal, setShowModal] = useState(false);

  const handleClose = () => setShowModal(false);
  const handleShow = () => setShowModal(true);
  const handleAccept = () => {
    createAnswer(peerId);
    handleClose();
  };

  return (
    <>
      <Modal show={showModal} onHide={handleClose}>
        <Modal.Header closeButton>
          <Modal.Title>Incoming Call</Modal.Title>
        </Modal.Header>
        <Modal.Body>A call has been received. Would you like to accept it?</Modal.Body>
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

export default Receiver;
