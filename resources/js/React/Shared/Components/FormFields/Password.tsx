import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    title: string;
    required?: boolean;
}

const Password: React.FC<Props> = ({ id, title, required = false }) => {
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
                        type="password"
                        id={id}
                        autoComplete={"off"}
                        placeholder={"Enter a value for " + title}
                        className={errors[id] ? "is-invalid" : ""}
                        {...register(id, {
                            required: required
                                ? "This field is required"
                                : false,
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
                                `${title} is required`}
                        </div>
                    )}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Password;
