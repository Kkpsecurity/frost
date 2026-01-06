import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    value?: any;
    title: string;
    required?: boolean;
}

const Email: React.FC<Props> = ({ id, value, title, required = false }) => {
    const {
        register,
        formState: { errors },
    } = useFormContext();

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>
                        {title}
                        {required && <span className="text-danger">*</span>}
                    </Form.Label>
                </Col>
                <Col lg={8}>
                    <Form.Control
                        type="email"
                        id={id}
                        placeholder={"Enter a value for " + title}
                        defaultValue={value}
                        className={errors[id] ? "is-invalid" : ""}
                        {...register(id, {
                            required: required
                                ? "This field is required"
                                : false,
                            pattern: {
                                value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                                message: "Please enter a valid email address",
                            },
                        })}
                        style={{
                            backgroundColor: "#2c3e50",
                            border: errors[id]
                                ? "1px solid #e74c3c"
                                : "1px solid #7f8c8d",
                            color: "white",
                            padding: "5px",
                            fontSize: "1rem",
                        }}
                    />
                    {errors[id] && (
                        <div className="invalid-feedback">
                            <i className="fa fa-exclamation"></i>{" "}
                            {errors[id]?.message?.toString() ||
                                `Please enter a valid ${title}.`}
                        </div>
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Email;
