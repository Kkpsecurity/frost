import React from 'react'

interface FrostStudentVideoChatProps {
    /**
     * The logged in user ID
     * This either the instructor or the student the auth user
     */
    laravelUserId: number | null;

    /**
     * The selected student ID
     */
    selectedStudentId: number | null;

    /**
     * Is the logged in user an instructor
     */
    isInstructor: boolean;

    /**
     * The course date ID
     */
    course_date_id: number;

    /**
     * Is there an incoming call request
     */
    inComingRequest: boolean;

    /**
     * Has the student accepted the call
     */
    callAccepted: boolean;

    /**
     * Accept the call amd End the call
     * @returns
     */
    handleAcceptCall: () => void;
    handleEndCall: () => void;
}

const FrostStudentVideoChat: React.FC<FrostStudentVideoChatProps> = ({
    laravelUserId, // The logged in user ID
    isInstructor, // Is the logged in user an instructor
    selectedStudentId, // The selected student ID
    course_date_id, // The course date ID
    inComingRequest, // Is there an incoming call request from the instructor
    callAccepted, // Has the student accepted the call
    handleAcceptCall,
    handleEndCall,
}) => {
  return (
    <div>FrostStudentVideoChat</div>
  )
}

export default React.memo(FrostStudentVideoChat);





// import React, { useEffect, useRef, useState } from "react";
// import AgoraRTM from "agora-rtm-sdk";
// import VideoControls from "./Partials/VideoControls";
// import WebRTMAgoraPeer from "./blocks/WebRTMAgoraPeer";

// import {
//     LocalVideoPlayer,
//     RemoteVideoPlayer,
//     VideoControlsContainer,
//     VideoPlayerContainer,
// } from "../../../../Styles/StyledVideoComponenet.styled";

// /**
//  * Google Stun Servers
//  */
// const servers = {
//     iceServers: [
//         {
//             urls: [
//                 "stun:stun1.l.google.com:19302",
//                 "stun:stun2.l.google.com:19302",
//             ],
//         },
//     ],
// };

// interface FrostStudentVideoChatProps {
//     /**
//      * The logged in user ID
//      * This either the instructor or the student the auth user
//      */
//     laravelUserId: number | null;

//     /**
//      * The selected student ID
//      */
//     selectedStudentId: number | null;

//     /**
//      * Is the logged in user an instructor
//      */
//     isInstructor: boolean;

//     /**
//      * The course date ID
//      */
//     course_date_id: number;

//     /**
//      * Is there an incoming call request
//      */
//     inComingRequest: boolean;

//     /**
//      * Has the student accepted the call
//      */
//     callAccepted: boolean;

//     /**
//      * Accept the call amd End the call
//      * @returns
//      */
//     handleAcceptCall: () => void;
//     handleEndCall: () => void;
// }

// const FrostStudentVideoChat: React.FC<FrostStudentVideoChatProps> = ({
//     laravelUserId, // The logged in user ID
//     isInstructor, // Is the logged in user an instructor
//     selectedStudentId, // The selected student ID
//     course_date_id, // The course date ID
//     inComingRequest, // Is there an incoming call request from the instructor
//     callAccepted, // Has the student accepted the call
//     handleAcceptCall,
//     handleEndCall,
// }) => {
//     console.log("FrostStudentVideoChat", laravelUserId, selectedStudentId);

//     if (selectedStudentId === undefined) {
//         selectedStudentId = null;
//     }

//     const [localStream, setLocalStream] = useState<MediaStream | null>(null);
//     const [remoteStream, setRemoteStream] = useState<MediaStream | null>(null);

//     const localVideoRef = useRef<HTMLVideoElement>(null);
//     const remoteVideoRef = useRef<HTMLVideoElement>(null);

//     const [client, setClient] = useState(null);
//     const [channel, setChannel] = useState(null);

//     let {
//         agoraConfig,
//         setAgoraConfig,
//         handleMuteStream,
//         handelUserJoined,
//         handleMessagesFromPeer,
//         createOfferForRemote,
//     } = WebRTMAgoraPeer({
//         laravelUserId,
//         isInstructor,
//         selectedStudentId,
//         course_date_id,
//         inComingRequest,
//         servers,
//         localStream,
//         remoteStream,
//         setLocalStream,
//         setRemoteStream,
//         localVideoRef,
//         remoteVideoRef,
//         channel,
//         client,
//     });

//     /**
//      * The Agora and RTM/WebRTC Config
//      */
//     useEffect(() => {
//         let agoraClient;
//         let agoraChannel;
//         let localMediaStream;

//         const init = async () => {
//             if (!agoraConfig) return;

//             try {
//                 agoraClient = AgoraRTM.createInstance(agoraConfig.appID);
//                 await agoraClient.login({
//                     uid: laravelUserId.toString(),
//                     token: agoraConfig.token,
//                 });

//                 setClient(agoraClient); // Set the client

//                 console.log(
//                     isInstructor
//                         ? "Creating The Channel"
//                         : "Joining The Channel",
//                     agoraConfig.channelName
//                 );
//                 agoraChannel = agoraClient.createChannel(
//                     agoraConfig.channelName
//                 );
//                 await agoraChannel.join();

//                 agoraChannel.on("MemberJoined", handelUserJoined);
//                 agoraClient.on("MessageFromPeer", handleMessagesFromPeer);

//                 try {
//                     localMediaStream =
//                         await navigator.mediaDevices.getUserMedia({
//                             video: true,
//                             audio: false,
//                         });

//                     if (localVideoRef.current) {
//                         localVideoRef.current.srcObject = localMediaStream;
//                     }
//                     await createOfferForRemote(laravelUserId.toString());
//                 } catch (err) {
//                     console.error("Failed to get user media", err);
//                 }
//             } catch (err) {
//                 console.error("Agora RTM login or channel joining failed", err);
//             }
//         };

//         init();

//         return () => {
//             if (client) {
//                 if (agoraChannel)agoraChannel.leave();              
//                 client.logout();

//                 if (localVideoRef.current) localVideoRef.current.srcObject = null;               
//                 if (remoteVideoRef.current) remoteVideoRef.current.srcObject = null;               
//             }
//         };
//     }, [
//         agoraConfig,
//         client,
//         handelUserJoined,
//         handleMessagesFromPeer,
//         createOfferForRemote,
//     ]);

//     return (
//         <>
//             <VideoPlayerContainer id="video-container">
//                 <LocalVideoPlayer
//                     ref={localVideoRef}
//                     playsInline
//                     autoPlay
//                 ></LocalVideoPlayer>

//                 <RemoteVideoPlayer
//                     ref={remoteVideoRef}
//                     playsInline
//                     autoPlay
//                 ></RemoteVideoPlayer>

//                 <VideoControlsContainer>
//                     <VideoControls
//                         handleEndCall={handleEndCall}
//                         handleAcceptCall={handleAcceptCall}
//                         handleMuteStream={handleMuteStream}
//                         callAccepted={callAccepted}
//                         remoteStream={remoteStream}
//                     />
//                 </VideoControlsContainer>
//             </VideoPlayerContainer>
//         </>
//     );
// };

// export default FrostStudentVideoChat;
