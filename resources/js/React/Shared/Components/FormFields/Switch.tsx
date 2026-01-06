import React, { useEffect, useState } from "react";
import { Form, Row, Col } from "react-bootstrap";
import BootstrapSwitchButton from "bootstrap-switch-button-react";
import { useFormContext } from "react-hook-form";

const Switch = ({ id, value, title, required = false }) => {
    const [isChecked, setIsChecked] = useState(false);
    const { register, setValue } = useFormContext();

    useEffect(() => {
        setIsChecked(value == undefined ? false : value);
    }, [value]);

    const handleSwitchChange = (checked: boolean) => {
        setIsChecked(checked);
        setValue(id, checked);
    };

    return (
        <Form.Group className="form-group">
            <Row>
                <Col lg={6}>
                    <Form.Label htmlFor={id}>
                        {title}
                        {required && <span className="text-danger">*</span>}
                    </Form.Label>
                </Col>
                <Col lg={6} className="text-end">
                    <BootstrapSwitchButton
                        checked={isChecked}
                        onlabel="Yes"
                        offlabel="No"
                        onChange={handleSwitchChange}
                    />
                </Col>
            </Row>
        </Form.Group>
    );
};

export default Switch;
