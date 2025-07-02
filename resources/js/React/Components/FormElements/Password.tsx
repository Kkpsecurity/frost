import React, { useEffect, useState } from "react";
import propTypes from "prop-types";
import { Form, Row, Col } from "react-bootstrap";
import { appendErrors, useFormContext } from "react-hook-form";

const Password = ({ id, title, required = false }) => {
    const { register } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>{title}</Form.Label>
                </Col>
                <Col lg={8}>
                    <Form.Control
                        type="password"
                        name={id}
                        autoComplete={"off"}
                        placeholder={"Enter a value for " + title}
                        {...register(id, { required: required })}
                    />
                    <Form.Control.Feedback type="invalid">
                        <i className="fa fa-exclimation"></i>
                    </Form.Control.Feedback>
                </Col>
            </Row>
        </Form.Group>
    );
};

Password.propTypes = {
    id: propTypes.string.isRequired,
    title: propTypes.string.isRequired,
    required: propTypes.bool,
};

export default Password;
