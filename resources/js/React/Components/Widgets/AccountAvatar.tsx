import React, { useState, useEffect } from "react";

type AccountAvatarProps = {
    avatar: string;
    width?: number;
    debug?: boolean;
};

const AccountAvatar: React.FC<AccountAvatarProps> = ({
    avatar,
    width = 180,
    debug = false,
}) => {
    const [localAvatar, setLocalAvatar] = React.useState<string>("");

    React.useEffect(() => {
        if (avatar.length == 0) {
            setLocalAvatar("assets/img/avatar.png");
        } else {
            setLocalAvatar(avatar);
        }
    }, [avatar]);

    return (
        <img
            src={localAvatar}
            id="avatar-image"
            className="Account_avatar__image"
            width={width}
            role="img"
        />
    );
};

export default AccountAvatar;
