import React, { useCallback } from "react";
import Tags from "@yaireo/tagify/dist/react.tagify"; // React-wrapper file
import "@yaireo/tagify/dist/tagify.css"; // Tagify CSS
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const Tagify = ({ id, value, title, required = false }) => {
    const { register, formState: { errors } } = useFormContext(); // retrieve all form-hooks methods
    const { onChange: registerOnChange } = register(id, { required: required });

    // on tag add/edit/remove
    const onChange = useCallback(
        (e) => {
            registerOnChange(e);
            console.log(
                "CHANGED:",
                e.detail.tagify.value, // Array where each tag includes tagify's (needed) extra properties
                e.detail.tagify.getCleanValue(), // Same as above, without the extra properties
                e.detail.value // a string representing the tags
            );
        },
        [registerOnChange]
    );

    return (
        <Form.Group className="form-group form-group-tagify">
            <Form.Label>{title}</Form.Label>
            <Tags
                id={id}
                defaultValue={value}
                onChange={onChange}
                style={{
                    backgroundColor: "#2c3e50",
                    border: errors[id]
                        ? "1px solid #e74c3c"
                        : "1px solid #7f8c8d",
                    color: "white",
                    padding: "5px",
                    fontSize: "1rem",
                }}

                // tagifyRef={tagifyRef}    // optional Ref object for the Tagify instance itself, to get access to  inner-methods
                // settings={settings}      // tagify settings object
                // {...tagifyProps}         // dynamic props such as "loading", "showDropdown:'abc'", "value"
            />
            <span
                className="float-end"
                style={{
                    fontSize: "10px",
                    textTransform: "uppercase",
                }}
            >
                Tagify
            </span>
        </Form.Group>
    );
};

export default Tagify;
