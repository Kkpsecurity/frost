import { yupResolver } from "@hookform/resolvers/yup";
import React, { useState } from "react";
import { Button, Form, Modal } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import * as yup from "yup";
import TextHidden from "../../Components/FormElements/TextHidden";
import Textarea from "../../Components/FormElements/Textarea";
import Loader from "../../Components/Widgets/Loader";

const BanModal = ({
    student,
    showBanModal,
    setShowBanModal,
    banLoading,
    setBanLoading,
    HandleBanStudent,
}) => {
    if (!student) return null;

    const schema = yup.object().shape({
        banReason: yup.string().required(),
    });

    const methods = useForm<FormData>({
        resolver: yupResolver(schema),
    });

    const TextMessage = `Banning ${student.fname} ${student.lname} will result in an automatic failure for the course and prevent completion of the class. This action is irreversible and can only be reversed by a System Administrator. Are you sure you want to ban ${student.fname} ${student.lname}?`;

    return (
        <Modal
          show={showBanModal}
          onHide={() => setShowBanModal(false)}
          backdrop="static"
          keyboard={false}
        >
          {banLoading ? (
            <div className="modal-loader">
              <Loader />
            </div>
          ) : (
            <FormProvider {...methods}>
              <form onSubmit={methods.handleSubmit(HandleBanStudent)}> {/* Note the change to camelCase for handleBanStudent */}
                <Modal.Header closeButton>
                  <Modal.Title>Ban {student.fname} {student.lname}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                  <div className="alert alert-danger">
                    <h3 className="text-warning">Permanent Action!</h3>
                    {TextMessage}
                  </div>
      
                  <TextHidden
                    id="studentUnitId"
                    value={student.student_unit_id}
                  />
      
                  <div className="form-group">
                    <Textarea
                      id="banReason"
                      title="Reason for Ban"
                      required={true}
                    />
                  </div>
                </Modal.Body>
                <Modal.Footer>
                  <Button
                    variant="secondary"
                    onClick={() => setShowBanModal(false)}
                  >
                    Close
                  </Button>
                  <Button type="submit" variant="primary">
                    Ban {student.fname} {student.lname}
                  </Button>
                </Modal.Footer>
              </form>
            </FormProvider>
          )}
        </Modal>
      );
      
};

export default BanModal;
