import React, { useEffect, useState } from "react";
import {
    createClient,
    createMicrophoneAndCameraTracks,
    ClientConfig,
    IAgoraRTCRemoteUser,
    AgoraVideoPlayer,
} from "agora-rtc-react";
import { AgoraVideoConfigType } from "../../../../../Config/types";
import PhoneIcons from "../Partials/player/PhoneIcons";

const config: ClientConfig = {
    mode: "rtc",
    codec: "vp8",
};

const useClient = createClient(config);
const useMicrophoneAndCameraTracks = createMicrophoneAndCameraTracks();

interface AgoraFrostPlayerProps {
    agoraConfig: AgoraVideoConfigType;
    callStudent: boolean;
    callStudentId: number;
    acceptedUserId: number;
    callHasEnded: boolean;
    activeCallRequest: boolean;
}

const AgoraFrostPlayer: React.FC<AgoraFrostPlayerProps> = ({
    agoraConfig,
    callStudent,
    callStudentId,
    acceptedUserId,
    callHasEnded,
    activeCallRequest,
}) => {
    const client = useClient();
    const { ready, tracks } = useMicrophoneAndCameraTracks();
    const [users, setUsers] = useState<IAgoraRTCRemoteUser[]>([]);
    const [joined, setJoined] = useState<boolean>(false);

    useEffect(() => {
        if (!client || !agoraConfig) return;

        const init = async () => {
            console.log("Init Agora");
            client.on("user-published", async (user, mediaType) => {
                console.log("user-published-before", user, mediaType);
                await client.subscribe(user, mediaType);
                console.log("subscribe success");
                if (mediaType === "video") {
                    setUsers((prevUsers) => [...prevUsers, user]);
                }
                if (mediaType === "audio") {
                    user.audioTrack?.play();
                }

                if (tracks && !joined) {
                    await client.publish(tracks);
                    setJoined(true);
                }
            });

            // client.on("user-unpublished", (user, mediaType) => {
            //     if (mediaType === "audio") {
            //         user.audioTrack?.stop();
            //     } else if (mediaType === "video") {
            //         setUsers((prevUsers) =>
            //             prevUsers.filter((User) => User.uid !== user.uid)
            //         );
            //     }
            // });

            // client.on("user-left", (user) => {
            //     setUsers((prevUsers) =>
            //         prevUsers.filter((User) => User.uid !== user.uid)
            //     );
            // });

            if (
                agoraConfig.token &&
                agoraConfig.appId &&
                agoraConfig.channelName
            ) {
                await client.join(
                    agoraConfig.appId,
                    agoraConfig.channelName,
                    agoraConfig.token,
                    null
                );
            }
        };

        init();

        return () => {
            if (tracks) {
                tracks.forEach((track) => track.stop());
                tracks.forEach((track) => track.close());
            }
            client.leave();
        };
    }, [client, ready, tracks, agoraConfig, joined]);

    if (!agoraConfig) {
        return <PhoneIcons calling="idle" />;
    }

    return (
        <>
            {callStudent ? (
                // The student has accepted the call
                acceptedUserId === callStudentId ? (
                    <>
                        <AgoraVideoPlayer
                            videoTrack={users[0].videoTrack}
                            style={{
                                width: "100%",
                                height: "100%",
                            }}
                        />
                    </>
                ) : (
                    <PhoneIcons calling="waiting_for_accept" />
                )
            ) : callStudentId ? (
                <PhoneIcons calling="calling" />
            ) : (
                <PhoneIcons calling="idle" />
            )}
        </>
    );
};

export default AgoraFrostPlayer;
