import React from "react";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    value?: any;
    title: string | false;
    required?: boolean;
}

const TextInput: React.FC<Props> = ({ id, value, title, required = false }) => {
    const { register, formState: { errors } } = useFormContext();

    return (
        <Form.Group className="mb-3">
            {title !== false && (
                <Form.Label htmlFor={id} style={{ color: "white", fontWeight: "500" }}>
                    {title}
                    {required && <span style={{ color: "#e74c3c" }}>*</span>}
                </Form.Label>
            )}
            <Form.Control
                type="text"
                id={id}
                placeholder={title !== false ? `Enter ${title.toLowerCase()}` : "Enter value"}
                defaultValue={value}
                className={`form-control` + errors[id] ? 'is-invalid' : ''}
                {...register(id, { required: required ? 'This field is required' : false })}
                style={{
                    backgroundColor: "#2c3e50",
                    border: errors[id] ? "1px solid #e74c3c" : "1px solid #7f8c8d",
                    color: "white",
                    padding: "5px",
                    fontSize: "1rem",
                }}
            />
            {errors[id] && (
                <div className="invalid-feedback">
                    <i className="fa fa-exclamation"></i>
                    {' '}
                    {errors[id]?.message?.toString() || `Please enter a valid ${title !== false ? title.toLowerCase() : 'value'}.`}
                </div>
            )}
        </Form.Group>
    );
};

export default TextInput;
