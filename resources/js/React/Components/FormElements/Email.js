import React, { useState, useEffect } from "react";
import propTypes from "prop-types";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const Email = ({ id, value, title, required = false }) => {
    const [text, setText] = useState(value);
    const { register } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>{title}</Form.Label>
                </Col>
                <Col lg={8}>
                    <Form.Control
                        type="email"
                        name={id}
                        id={id}
                        placeholder={"Enter a value for " + title}
                        value={text}
                        onChange={(e) => setText(e.target.value)}
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

Text.propTypes = {
    id: propTypes.string.isRequired,
    value: propTypes.any,
    title: propTypes.string.isRequired,
    required: propTypes.bool,
};

export default Email;
