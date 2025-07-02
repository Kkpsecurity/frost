import React, { useCallback } from "react";
import Tags from "@yaireo/tagify/dist/react.tagify"; // React-wrapper file
import "@yaireo/tagify/dist/tagify.css"; // Tagify CSS
import propTypes from "prop-types";
import { Form } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

const Tagify = ({ id, value, title, required = false, register }) => {
    // on tag add/edit/remove
    const onChange = useCallback((e) => {
        console.log(
            "CHANGED:",
            e.detail.tagify.value, // Array where each tag includes tagify's (needed) extra properties
            e.detail.tagify.getCleanValue(), // Same as above, without the extra properties
            e.detail.value // a string representing the tags
        );
    }, []);

    const { register } = useFormContext(); // retrieve all form-hooks methods

    return (
        <Form.Group className="form-group form-group-tagify">
            <Form.Label>{title}</Form.Label>
            <Tags
                ref={register}
                id={id}
                name={id}
                defaultValue={value}
                onChange={onChange}
                {...register(id, { required: required })}

                // tagifyRef={tagifyRef}    // optional Ref object for the Tagify instance itself, to get access to  inner-methods
                // settings={settings}      // tagify settings object
                // {...tagifyProps}         // dynamic props such as "loading", "showDropdown:'abc'", "value"
            />
            <span
                className="float-end"
                style={{
                    fontSize: "10px",
                    fontTransform: "uppercase",
                }}
            >
                Tagify
            </span>
        </Form.Group>
    );
};

Tagify.propTypes = {
    id: propTypes.string.isRequired,
    value: propTypes.any,
    title: propTypes.string.isRequired,
    required: propTypes.bool,
};

export default Tagify;
