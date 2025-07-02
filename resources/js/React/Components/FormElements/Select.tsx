import React from "react";
import { Form, Row, Col } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    options: { text: string; value: string }[];
    title: string;
    value: any;
    required?: boolean;
}

const Select: React.FC<Props> = ({ id, options, title, value = "", required = false }) => {
    const { register, formState: { errors } } = useFormContext(); // Destructure errors from formState

    return (
        <Form.Group className="form-group mb-3">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>{title}</Form.Label>
                </Col>
                <Col lg={8}>
                    <select
                        id={id}
                        name={id}
                        defaultValue={value}
                        className={`form-control ${errors[id] ? 'is-invalid' : ''}`} // Add 'is-invalid' class if there is an error
                        {...register(id, { required: required ? 'This field is required' : false })}
                    >
                        {options.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.text}
                            </option>
                        ))}
                    </select>
                    {errors[id] && <div className="invalid-feedback">{errors[id]?.message?.toString()}</div>}
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Select;
