import React from "react";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    title?: string;
    value?: any;
    required?: boolean;
}

const DatePicker: React.FC<Props> = ({ id, title, value, required = false }) => {
    const { register, formState: { errors } } = useFormContext();

    return (
        <Form.Group className="mb-3">
            {title && (
                <Form.Label htmlFor={id} style={{ color: "white", fontWeight: "500" }}>
                    {title}
                    {required && <span style={{ color: "#e74c3c" }}>*</span>}
                </Form.Label>
            )}
            <Form.Control
                type="date"
                id={id}
                placeholder={title ? "Enter " + title.toLowerCase() : "Select a date"}
                defaultValue={value}
                className={errors[id] ? 'is-invalid' : ''}
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
                    {errors[id]?.message?.toString() || "Please select a valid date"}
                </div>
            )}
        </Form.Group>
    );
};

export default DatePicker;
