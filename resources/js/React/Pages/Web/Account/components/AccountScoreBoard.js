import React, { useContext, useEffect, useState } from "react";
import propTypes from "prop-types";
import moment from "moment";

import SocialMediaIcons from "./SocialMediaIcons";

/**
 * @param
 * @returns
 */
const AccountScoreBoard = ({ user, debug = false }) => {
    if (debug === true) console.log("Scoreboard Loaded");

    const [fullname, setFullname] = useState("");
    const [dateJoined, setDateJoined] = useState("");

    useEffect(() => {
        setFullname(user.fname + " " + user.lname);
        setDateJoined(moment(user.created_at).calendar());
    }, [user]);

    return (
        <div className="account-scoreboard">
            <h3 className="text-white m-1">{fullname}</h3>
            <p className="text-white-50">
                <i className="fa fa-calendar" /> Date Joined: {dateJoined}
            </p>
            <hr />
            <SocialMediaIcons user={user} debug={debug} />
        </div>
    );
};

AccountScoreBoard.propTypes = {
    user: propTypes.object.isRequired,
    debug: propTypes.bool,
};

export default AccountScoreBoard;
