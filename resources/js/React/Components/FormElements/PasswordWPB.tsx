import React, { useEffect, useState } from "react";
import propTypes from "prop-types";
import { Form, Row, Col, ProgressBar } from "react-bootstrap";
import { useFormContext } from "react-hook-form";

interface Props {
    id: string;
    title: string;
    required?: boolean;
}

const PasswordWPB: React.FC<Props> = ({ id, title, required = false }) => {
    const [score, setScore] = useState(0);
    const [variant, setVariant] = useState("");
    const { register } = useFormContext(); // retrieve all form-hooks methods

    // rules
    const [totalChar, setTotalChar] = useState(0);
    const [capitalLetters, setCapitalLetters] = useState(0);
    const [smallLetters, setSmallLetters] = useState(0);
    const [specialChar, setSpecialChar] = useState(0);
    const [numricalChar, setNumricalChar] = useState(0);

    const variantClasses = [
        { scoreRange: [0, 10], variant: "danger" },
        { scoreRange: [11, 20], variant: "dangerLight" },
        { scoreRange: [21, 40], variant: "warning" },
        { scoreRange: [41, 70], variant: "successLight" },
        { scoreRange: [71, 100], variant: "success" },
    ];

    useEffect(() => {
        const updateRulesAndVariant = () => {
            let myScore =
                totalChar +
                capitalLetters +
                smallLetters +
                specialChar +
                numricalChar;

            console.log(myScore);
            setScore(myScore);

            const matchingVariant = variantClasses.find((vc) => {
                return (
                    vc.scoreRange[0] <= myScore && vc.scoreRange[1] >= myScore
                );
            });

            setVariant(matchingVariant ? matchingVariant.variant : "");
        };

        updateRulesAndVariant();
    }, [totalChar, capitalLetters, smallLetters, specialChar, numricalChar]);

    const passwordStrength = (Event) => {
        const password = Event.target.value;

        if (password.length == 8) {
            setTotalChar(5);
        } else if (password.length < 8) {
            setTotalChar(0);
        }

        if (password.length >= 16) {
            setTotalChar(20);
        } else if (password.length < 16 && password.length > 12) {
            setTotalChar(10);
        }

        if (password.match(/[a-z]/)) {
            setSmallLetters(10);
        } else {
            setSmallLetters(0);
        }

        if (password.match(/[A-Z]/)) {
            setCapitalLetters(20);
        } else {
            setCapitalLetters(0);
        }

        if (password.match(/\d+/g)) {
            setNumricalChar(20);
        } else {
            setNumricalChar(0);
        }

        if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) {
            setSpecialChar(20);
        } else {
            setSpecialChar(0);
        }
    };

    return (
        <Form.Group className="form-group m-2">
            <Row>
                <Col lg={4}>
                    <Form.Label htmlFor={id}>{title}</Form.Label>
                </Col>
                <Col lg={8}>
                    <Form.Control
                        type="password"
                        name={id}
                        id={id}
                        autoComplete={"off"}
                        placeholder={"Enter a value for " + title}
                        onKeyUp={passwordStrength}
                        {...register(id, { required: required })}
                    />
                    <Form.Control.Feedback type="invalid">
                        <i
                            className="fa fa-exclamation-triangle"
                            aria-hidden="true"
                        ></i>{" "}
                        {title} is required
                    </Form.Control.Feedback>
                </Col>
            </Row>
            <ProgressBar
                className="my-3"
                now={score}
                max={100}
                variant={variant}
            ></ProgressBar>
        </Form.Group>
    );
};

export default PasswordWPB;
