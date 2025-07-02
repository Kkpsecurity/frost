import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";
import InputMask from 'react-input-mask';

const PhoneInput = ({ id, value, title, required = false, mask = "999-999-9999" }) => {
    const { register, formState: { errors } } = useFormContext();

    return (
        <Form.Group className="form-group m-2">
            <Row>
                {title === false ? null : (
                    <Col lg={4}>
                        <Form.Label htmlFor={id}>{title}</Form.Label>
                    </Col>
                )}
                <Col lg={title === false ? 12 : 8}>
                    <InputMask
                        mask={mask}
                        maskChar=" "
                        className="form-control"
                        id={id}
                        placeholder={"Enter " + title}
                        defaultValue={value}
                        {...register(id, { required: required })}
                    />
                    {errors[id] && (
                        <Form.Control.Feedback type="invalid">
                            <i className="fa fa-exclamation"></i>
                            Please enter a valid {title}.
                        </Form.Control.Feedback>
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default PhoneInput;
