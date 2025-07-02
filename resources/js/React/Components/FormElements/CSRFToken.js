import React from "react";
import { Form } from "react-bootstrap";
import propTypes from "prop-types";
import { useFormContext } from "react-hook-form";

const TextHidden = () => {
    const { register } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Control
            type="hidden"
            name="_token"
            value={document.querySelector('[name="csrf-token"]').content}
            id="_token"
            {...register("_token")}
        />
    );
};

export default TextHidden;
