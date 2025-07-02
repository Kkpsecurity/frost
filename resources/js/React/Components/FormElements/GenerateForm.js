import React from "react";

const GenerateForm = ({ config }) => {
    return config.inputs.map((input) => {
        return React.createElement(input.children[0].component, {
            key: input.id,
            id: input.id,
            title: input.title,
            required: input.required,
            value: input.value,
        });
    });
};

export default GenerateForm;
