import React, { useState } from "react";
import { Alert, Form } from "react-bootstrap";
import propTypes from "prop-types";
import { useFormContext } from "react-hook-form";

const Textarea = ({ id, value, title, required }) => {
    const [text, setText] = useState(value);
    const {
        register,
        formState: { errors },
    } = useFormContext();

    return (
        <Form.Group className="form-group">
            <Form.Label>{title}</Form.Label>
            <Form.Control
                as="textarea"
                rows={3}
                name={id}
                id={id}
                value={text}
                {...register(id, { required: required })}
                onChange={(e) => setText(e.target.value)}
            />
            {errors[id] && (
                <Alert variant="danger" className="mt-2">
                    {errors[id].type === "required" && (
                        <span>{`${title} is required`}</span>
                    )}
                </Alert>
            )}
        </Form.Group>
    );
};

Textarea.propTypes = {
    id: propTypes.string.isRequired,
    value: propTypes.any,
};

export default Textarea;
