import React, { useState, useEffect } from 'react';
import { AgoraVideoPlayer } from 'agora-rtc-react';

/**
 * MuiltiChat Window Support for the Video Chat
 * @param props 
 * @returns 
 */
const MuiltiChat = (props) => {
    const {ready, users, tracks, inCall} = props;
    const [colspacing, setColspacing] = useState(12);

    const [height] = useState("280px");
    const [width] = useState("100%");

    // To Handel the size spacing of the videos
    useEffect(() => {
        setColspacing(Math.max(Math.floor(12 / (users.length + 1)), 4));
    }, [users, tracks]);

    if(users.length < 0) {
        return <div className="alert alert-danger">No Users Stream Found</div>;
    }

    return (
        <div className="player_screens">
            <div style={{height: height, width: "50%", float: "left", border: "2px solid #222", background: "#444", objectFit: "contain"}}>
                <AgoraVideoPlayer
                    videoTrack={tracks[1]}
                    style={{ height: height, width: width }} />
            </div>
            <div style={{height: height, width: "50%", float: "right", border: "2px solid #222", background: "#777", objectFit: "contain"}}>
                {users.length > 0 && users.map((user, index) => {
                    if(user.videoTrack) {
                        return (
                            <AgoraVideoPlayer
                                key={index}
                                videoTrack={user.videoTrack}
                                style={{ height: height, width: width }} />
                        );
                    } else {
                        return <div className="alert alert-danger">User Video Track failed to stream</div>;
                    }
                })}
            </div>
        </div>
    );
};

export default MuiltiChat;
