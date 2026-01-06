import React from "react";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    title: string;
    required?: boolean;
}

const CheckBox: React.FC<Props> = ({ id, title, required = false }) => {
    const { register, formState: { errors } } = useFormContext();

    return (
        <Form.Group className="mb-3">
            <Form.Check
                type="checkbox"
                id={id}
                name={id}
                className={errors[id] ? 'is-invalid' : ''}
                style={{
                    fontSize: "0.95rem"
                }}
                label={
                    <span style={{ color: "white", fontSize: "0.95rem", cursor: "pointer", marginLeft: "0.5rem" }}>
                        {title}
                        {required && <span style={{ color: "#e74c3c" }}>*</span>}
                    </span>
                }
                {...register(id, { required: required ? 'This field is required' : false })}
            />
            {errors[id] && (
                <div className="invalid-feedback d-block">
                    <i className="fa fa-exclamation"></i>
                    {' '}
                    {errors[id]?.message?.toString() || `Please check ${title}.`}
                </div>
            )}
        </Form.Group>
    );
};

export default CheckBox;
