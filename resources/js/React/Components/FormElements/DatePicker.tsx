import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const DatePicker = ({ id, title, value, required = false }) => {
    const { register, formState: { errors } } = useFormContext();

    return (
        <Form.Group className="form-group m-2">
            <Row>
                {title && (
                    <Col lg={4}>
                        <Form.Label htmlFor={id}>{title}</Form.Label>
                    </Col>
                )}
                <Col lg={title ? 8 : 12}>
                    <Form.Control
                        type="date"
                        id={id}
                        placeholder={"Enter a value for " + title}
                        {...register(id, { required })}
                        isInvalid={!!errors[id]}
                    />
                    {/* Ensure error message is a string and only render if it exists */}
                    {errors[id] && (
                        <Form.Control.Feedback type="invalid">
                            <>{errors[id].message || "Invalid input"}</>
                        </Form.Control.Feedback>
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default DatePicker;
