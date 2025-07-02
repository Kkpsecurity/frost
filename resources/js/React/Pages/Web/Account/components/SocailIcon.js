import React from "react";
import { Button } from "react-bootstrap";

const SocailIcon = ({ href, social }) => {
    return (
        <a
            href={href}
            className={"btn btn-circle bg-" + social}
            target="_blank"
        >
            <i className={"fab fa-" + social + " fa-2x"} />
        </a>
    );
};

export default SocailIcon;
