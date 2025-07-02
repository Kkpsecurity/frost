import React, { useEffect, useRef, useState } from "react";
import initAgora from "../settings.js";

const WebRTMAgoraPeer = (props) => {
    let {
        laravelUserId,
        isInstructor,
        selectedStudentId,
        course_date_id,
        inComingRequest,
        servers,
        localStream,
        remoteStream,
        setLocalStream,
        setRemoteStream,
        localVideoRef,
        remoteVideoRef,
        channel,
        client,
    } = props;

  
    const [peerConnection, setPeerConnection] = useState<RTCPeerConnection>(
        null
    );

    /**
     * The Agora Config
     */
    const [agoraConfig, setAgoraConfig] = useState<any>(null);

    /**
     * Mute the stream audio or video
     */
    const handleMuteStream = (type: "audio" | "video"): boolean => {
        if (!["audio", "video"].includes(type)) {
            console.error(
                'Invalid track type for mute operation. Must be "audio" or "video".'
            );
            return false;
        }

        try {
            localStream.getTracks().forEach((track) => {
                if (track.kind === type) {
                    track.enabled = !track.enabled;
                }
            });

            setLocalStream((prevStream) => ({ ...prevStream })); // Trigger state update to reflect the changes

            return true;
        } catch (error) {
            console.error(`Failed to mute ${type} track: `, error);
            return false;
        }
    };

    /**
     * Handels The Remote user Joining the channel
     * @param MemberID
     */
    const handelUserJoined = async (MemberID) => {
        console.log("MemberJoined", MemberID);
        if (peerConnection) {
            await createOfferForRemote(MemberID);
        }
    };

    /**
     * Create a new RTCPeerConnection with the specified stun servers
     * @param MemberID
     */
    const createPeerConnection = async (MemberID) => {
        console.log("PeerConnection Started", MemberID);
        
        // Create a new RTCPeerConnection with the specified servers
        const newPeerConnection = new RTCPeerConnection(servers);
        setPeerConnection(newPeerConnection);

        // Setup remote stream
        setRemoteStream(new MediaStream());
        remoteVideoRef.current.srcObject = remoteStream;

        // Set up a counter for received tracks and expected tracks
        //  let receivedTrackCount = 0;
        // const expectedTrackCount = 2; // adjust this value based on the expected number of tracks

        if (!localStream) {
            // Request access to local media devices (video and audio)
            console.log("LocalStream Null reestablishing");
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false,
                });
                setLocalStream(stream);
                localVideoRef.current.srcObject = stream;
            } catch (error) {
                console.error("Failed to access local media devices: ", error);
                return;
            }
        }

        localStream.getTracks().forEach((track) => {
            console.log("Adding local track:", track);
            newPeerConnection.addTrack(track, localStream);
        });        

        // Handle ICE candidates
        newPeerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                if (client) {
                    client.sendMessageToPeer(
                        JSON.stringify({
                            type: "candidate",
                            candidate: event.candidate,
                        }),
                        MemberID
                    );
                }
            }
        };
    };

    const createOfferForRemote = async (MemberID: string): Promise<void> => {
        try {
            console.log("Creating Offer for remote", MemberID, localStream);
            
            await createPeerConnection(MemberID); // Wait for createPeerConnection to complete

            // Get the peer connection
            if (!peerConnection) {
                console.error("Peer connection is null.");
                return;
            }

            // Create an offer
            const offer: RTCSessionDescriptionInit = await peerConnection.createOffer();

            // Set the local description with the offer
            await peerConnection.setLocalDescription(offer);

            // Send the offer to the remote peer
            const response = await client.sendMessageToPeer(
                JSON.stringify({ type: "offer", offer }),
                MemberID
            );

            // If response is not successful, you can handle it here
            if (!response.hasPeerReceived) {
                console.error(`Failed to send offer to member ${MemberID}`);
            }
        } catch (error) {
            console.error("Failed to create offer for remote peer: ", error);
        }
    };

    /**
     * Receive the video offer from the instructor
     * @param offer
     * @param MemberID
     */
    const handelVideoOffer = async (offer, MemberID) => {
        await createPeerConnection(MemberID);

        // Set the remote description with the offer
        await peerConnection.setRemoteDescription(offer);

        // Create an answer
        let answer = await peerConnection.createAnswer();

        // Set the local description with the answer
        await peerConnection.setLocalDescription(answer);

        if (client) {
            // Send the answer to the instructor
            client.sendMessageToPeer(
                JSON.stringify({
                    type: "answer",
                    answer: answer,
                }),
                MemberID
            );
        }
    };

    /**
     * The Agora RTM Sends Answer for remote
     * @param answer
     */
    const handelAnswerForVideoOffer = async (answer) => {
        if (!peerConnection.currentRemoteDescription) {
            await peerConnection.setRemoteDescription(answer);
        }
    };

    /**
     * The Agora RTM Handel Message From Peer
     * @param message
     * @param MemberID
     */
    const handleMessagesFromPeer = async (message, MemberID) => {
        const messageData = JSON.parse(message.text);

        if (messageData.type === "offer") {
            await handelVideoOffer(messageData.offer, MemberID);
        } else if (messageData.type === "answer") {
            handelAnswerForVideoOffer(messageData.answer);
        } else if (messageData.type === "candidate") {
            if (
                peerConnection &&
                (peerConnection.signalingState === "have-remote-offer" ||
                    peerConnection.signalingState === "stable")
            ) {
                await peerConnection.addIceCandidate(
                    new RTCIceCandidate(messageData.candidate)
                );
            }
        }
    };

    /**
     * We Setup a debounced function to limit request
     *
     * Only when the The selectedStudentId
     *
     * we trigger the initAgora function
     * or everytime the selectedStudentId changes
     * we all so check if there is an incoming request
     * if there is an incoming request then we trigger the initAgora function
     */
    const debounce = (func, delay) => {
        let debounceTimer;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    };

    const debouncedInstructorAgora = useRef(
        debounce(() => {
            initAgora(laravelUserId, course_date_id, "instructor")
                .then((data) => {
                    setAgoraConfig(data.agoraConfig);
                })
                .catch((error) => console.log(error));
        }, 1000)
    ).current;

    useEffect(() => {
        if (isInstructor && selectedStudentId) {
            debouncedInstructorAgora();
        }
    }, [isInstructor, laravelUserId, course_date_id, debouncedInstructorAgora]);

    const debouncedStudentAgora = useRef(
        debounce(() => {
            initAgora(laravelUserId, course_date_id, "student")
                .then((data) => {
                    setAgoraConfig(data.agoraConfig);
                })
                .catch((error) => console.log(error));
        }, 1000)
    ).current;

    useEffect(() => {
        if (!isInstructor && inComingRequest) {
            debouncedStudentAgora();
        }
    }, [
        inComingRequest,
        isInstructor,
        laravelUserId,
        course_date_id,
        debouncedStudentAgora,
    ]);

    return {
        localStream,
        remoteStream,
        channel,
        localVideoRef,
        remoteVideoRef,
        agoraConfig,
        setAgoraConfig,
        handleMuteStream,
        handelUserJoined,
        handleMessagesFromPeer,
        createOfferForRemote,
    };
};

export default WebRTMAgoraPeer;
