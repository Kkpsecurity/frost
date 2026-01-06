import React, { useEffect, useState } from "react";
import { Card, Col, Form, Row } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface GravatarInputProps {
    debug?: boolean;
}

const GravatarInput: React.FC<GravatarInputProps> = ({ debug = false }) => {
    if (debug === true) console.log("Gravatar Input Loaded!");

    const [gravatarEnabled, setGravatarEnabled] = useState(false);
    const { register, formState: { errors } } = useFormContext();

    return (
        <div className="ui-toggle p-3">
            <hr />
            <span className="lead text-bold">
                Enable Gravatar -
                <a
                    href="https://gravatar.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn btn-sm btn-link fs-14 float-end text-dark"
                >
                    Learn More.
                </a>
                <Form.Label className="switch p-2">
                    <Form.Check
                        type="switch"
                        id="enable_gravatar"
                        className={errors.enable_gravatar ? 'is-invalid' : ''}
                        {...register("enable_gravatar")}
                    />
                </Form.Label>
                <br />
                <p className="alert alert-danger text-sm">
                    <i>
                        Note: Your Registered Email must match that of your
                        Registered Gravatar Email.
                    </i>
                </p>
                {errors.enable_gravatar && (
                    <div className="invalid-feedback">
                        <i className="fa fa-exclamation"></i>
                        {' '}
                        {errors.enable_gravatar?.message?.toString() || 'Gravatar setting is invalid'}
                    </div>
                )}
            </span>
        </div>
    );
};

export default GravatarInput;
