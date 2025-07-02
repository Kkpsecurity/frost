import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const TextInput = ({ id, value, title, required = false }) => {
    const { register, formState: errors } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Group className="form-group m-2">
            <Row>
                {title === false ? null : (
                    <Col lg={4}>
                        <Form.Label htmlFor={id}>{title}</Form.Label>
                    </Col>
                )}
                <Col lg={title === false ? 12 : 8}>
                    <Form.Control
                        type="text"
                        id={id}
                        placeholder={"Enter a value for " + title}                      
                        defaultValue={value}
                        {...register(id, { required: required })}
                    />
                    <Form.Control.Feedback type="invalid">
                        <i className="fa fa-exclimation"></i>
                        Please enter a {title}.
                    </Form.Control.Feedback>
                </Col>
            </Row>
        </Form.Group>
    );
};

export default TextInput;
