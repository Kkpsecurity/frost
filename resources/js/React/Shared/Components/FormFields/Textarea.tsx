import React, { useState } from "react";
import { Alert, Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const Textarea = ({ id, value, title, required = false }) => {
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
                id={id}
                value={text}
                {...register(id, { required: required })}
                onChange={(e) => setText(e.target.value)}
                style={{
                    backgroundColor: "#2c3e50",
                    border: errors[id] ? "1px solid #e74c3c" : "1px solid #7f8c8d",
                    color: "white",
                    padding: "5px",
                    fontSize: "1rem",
                }}
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

export default Textarea;
