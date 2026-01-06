import React from "react";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    value: any;
}

const TextHidden: React.FC<Props> = ({ id, value }) => {
    const { register } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Control
            type="hidden"
            id={id}
            defaultValue={value}
            {...register(id)}
        />
    );
};


export default TextHidden;
