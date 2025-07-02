import React from "react";
import "./PhoneIcons.css";
import { FaPhone, FaPhoneSlash, FaPhoneSquareAlt, FaSpinner } from "react-icons/fa";

const iconSize = "4em";

const PhoneIcons = ({ calling = "idle" }) => {
    const renderIcon = () => {
        switch (calling) {
            case "incoming":
                return <FaPhone className="phone-ringing " style={{ color: "green" }} size={iconSize} />;          
            case "calling":
                return <FaPhone style={{ color: "lightblue" }} size={iconSize} />;
            case "initializing":
                return <FaSpinner style={{ color: "yellow" }} size={iconSize} />;
            case "waiting":
                return <FaPhone style={{ color: "dark" }} size={iconSize} />;
            case "idle":
                return <FaPhone style={{ color: "dark" }} size={iconSize} />;
            case "error":
                return <FaPhoneSlash style={{ color: "red" }} size={iconSize} />;
            default:
                return <FaPhone style={{ color: "red" }} size={iconSize} />;  
        }
        
    };

    return (
        <div
            className={`d-flex bg-dark svg-container`}
            style={{
                width: "100%",
                height: "100%",
                justifyContent: "center",
                alignItems: "center",
            }}
        >
            <div className={`svg-ring ${calling !== "idle" ? "calling" : "idle"}`}>
                {renderIcon()}
            </div>
        </div>
    );
};

export default PhoneIcons;
