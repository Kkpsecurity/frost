import React, { useRef } from "react";
import { Col, Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const nextBox = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { maxLength, value, name } = e.target;
    const [fieldName, fieldIndex] = name.split("_");

    let fieldIntIndex = parseInt(fieldIndex, 10);

    // Check if no of char in field == maxlength
    if (value.length >= maxLength) {
        // It should not be last input field
        if (fieldIntIndex < 4) {
            // Get the next input field using it's name
            const nextfield = document.getElementById(
                "code_" + (fieldIntIndex + 1)
            ) as HTMLInputElement;

            // If found, focus the next field
            console.log(nextfield);
            if (nextfield !== null) {
                nextfield.focus();
            }
        }
    }
};


const VerifyBox = ({ index }) => {
    const { register, formState: errors } = useFormContext();
    const nextInput = useRef(null);

    return (
        <Col lg={3}>
            <Form.Label
                htmlFor={"code_" + index}
                className="sr-only control-label"
            >
                Verification Required
            </Form.Label>
            <input
                type="text"
                id={"code_" + index}
                className="code_box form-control text-center required"
                maxLength={1}
                minLength={1}
                placeholder="•"
                {...register("code_" + index, { required: true })}
                ref={index === 4 ? undefined : nextInput}
                onChange={(e) => nextBox(e)}
                style={{
                    height: "75px",
                    fontSize: "36px",
                    textTransform: "uppercase",
                }}
            />

            {errors[`code_${index}`] && (
                <Form.Control.Feedback type="invalid">
                    <i className="fa fa-exclimation"></i>
                    Please enter Box {index}.
                </Form.Control.Feedback>
            )}
        </Col>
    );
};

export default VerifyBox;
