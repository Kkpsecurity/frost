import React from "react";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const CheckBox = ({ id, title, required = false }) => {
    const { register, formState: { errors } } = useFormContext(); 

    return (
        <Form.Group className="form-group m-2">
            <Form.Check 
                type="checkbox"
                id={id}
                label={title}
                {...register(id, { required })} 
            />
            {errors[id] && (
                <Form.Control.Feedback type="invalid">
                    <i className="fa fa-exclamation" />
                    Please enter a {title}.
                </Form.Control.Feedback>
            )}
        </Form.Group>
    );
};
export default CheckBox;
