import React, { useEffect, useState } from "react";
import { Form, Row, Col } from "react-bootstrap";
import BootstrapSwitchButton from "bootstrap-switch-button-react";
import { useFormContext } from "react-hook-form";

interface SwitchProps {
    id: string;
    value: any;
    title: string;
    required?: boolean;
    onValueChange: () => void;
}

const Switch: React.FC<SwitchProps> = ({
    id,
    value,
    title,
    required = false,
}) => {
    const [isPublished, setIsPublished] = useState(false);
    const { register } = useFormContext(); // retrieve all form-hooks methods

    useEffect(() => {
        setIsPublished(value == undefined ? false : true);
    }, [value]);

    return (
        <Form.Group className="form-group">
            <Row>
                <Col lg={6}>
                    <Form.Label htmlFor={id}>{title}</Form.Label>
                </Col>
                <Col lg={6} className="text-end">
                   <></>
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Switch;
