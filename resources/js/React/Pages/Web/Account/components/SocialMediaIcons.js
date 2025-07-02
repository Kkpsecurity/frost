import React, { useContext, useEffect, useState } from "react";
import SocailIcon from "./SocailIcon";

const SocialMediaUsers = ({ user, debug }) => {
    if (debug === true) console.log("SocialMedia Widget Laoded");

    const [facebookUser, setFacebookUser] = useState("");
    const [twitterUser, setTwitterUser] = useState("");
    const [youtubeUser, setYoutubeUser] = useState("");
    const [linkedinUser, setLinkedinUser] = useState("");
    const [pinterestUser, setPinterestUser] = useState("");

    useEffect(() => {
        setFacebookUser(user.facebook_username);
        setTwitterUser(user.twitter_username);
        setYoutubeUser(user.youtube_username);
        setLinkedinUser(user.linkedin_username);
        setPinterestUser(user.pinterest_username);
    }, [
        user.facebook_username,
        user.twitter_username,
        user.youtube_username,
        user.linkedin_username,
        user.pinterest_username,
    ]);

    const facebookIcon = (facebookUser) => {
        if (facebookUser !== undefined) {
            return (
                <SocailIcon
                    href={"http://facebook.com/" + facebookUser}
                    social="facebook"
                />
            );
        }
    };

    const twitterIcon = (twitterUser) => {
        if (twitterUser !== undefined) {
            return (
                <SocailIcon
                    href={"http://twitter.com/" + twitterUser}
                    social="twitter"
                />
            );
        }
    };

    const youtubeIcon = (youtubeUser) => {
        if (youtubeUser !== undefined) {
            return (
                <SocailIcon
                    href={"http://youtube.com/" + youtubeUser}
                    social="youtube"
                />
            );
        }
    };

    const linkedinIcon = (linkedinUser) => {
        if (linkedinUser !== undefined) {
            return (
                <SocailIcon
                    href={"http://linkedin.com/" + linkedinUser}
                    social="linkedin"
                />
            );
        }
    };

    const pinterestIcon = (pinterestUser) => {
        if (pinterestUser !== undefined) {
            return (
                <SocailIcon
                    href={"http://pinterest.com/" + pinterestUser}
                    social="pinterest"
                />
            );
        }
    };

    return (
        <React.Fragment>
            {facebookIcon(facebookUser)}
            {twitterIcon(twitterUser)}
            {youtubeIcon(youtubeUser)}
            {linkedinIcon(linkedinUser)}
            {pinterestIcon(pinterestUser)}
        </React.Fragment>
    );
};

export default SocialMediaUsers;
