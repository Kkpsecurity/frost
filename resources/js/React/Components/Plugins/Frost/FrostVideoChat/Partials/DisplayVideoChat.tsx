import React from 'react';
import {Row, Col, Container} from 'react-bootstrap';
import "../video.css";

interface displayVideoChatProps {
    videoAutoPlay: boolean;
    instructorVideoRef: React.RefObject<HTMLVideoElement>;
    studentVideoRef: React.RefObject<HTMLVideoElement>;
    startCallRef: React.RefObject<HTMLButtonElement>;
    muteVideoRef: React.RefObject<HTMLButtonElement>;
    muteMicRef: React.RefObject<HTMLButtonElement>;
}

const DisplayVideoChat: React.FC<displayVideoChatProps> = ({
    videoAutoPlay,
    instructorVideoRef,
    studentVideoRef,
    startCallRef,
    muteVideoRef,
    muteMicRef
}) => {
    return (
        <Row>
            <Col>
                <div className="videochat-container">
                    <Row>
                        <Col lg={5}>
                            <div className="videochat-video-block">
                                <video
                                    ref={instructorVideoRef}
                                    autoPlay={videoAutoPlay}
                                    id="instructorVideo"
                                    className="videochat-video"
                                    poster='/assets/img/video-call-icon-on-white.jpg'
                                    style={{
                                        width: "100%",
                                        height: "100%",
                                        objectFit: "contain"
                                    }}
                                ></video>
                            </div>
                        </Col>
                        <Col lg={2}>
                            <div className="videochat-controls">
                                <button
                                    ref={startCallRef}
                                    className="btn btn-primary btn-sm"
                                    id="startCall"
                                    style={{
                                        width: "100px",
                                        height: "50px",
                                    }}
                                >
                                    Start Call
                                </button>
                                <button
                                    ref={muteVideoRef}
                                    className="btn btn-primary btn-sm"
                                    id="muteVideo"
                                    style={{
                                        width: "100px",
                                        height: "50px",
                                    }}
                                >
                                    Mute Video
                                </button>
                                <button
                                    ref={muteMicRef}
                                    className="btn btn-primary btn-sm"
                                    id="muteMic"
                                    style={{
                                        width: "100px",
                                        height: "50px",
                                    }}
                                >
                                    Mute Mic
                                </button>
                            </div>
                        </Col>
                        <Col lg={5}>
                            <div className="videochat-video-block">
                                <video
                                    ref={studentVideoRef}
                                    autoPlay={videoAutoPlay}
                                    id="studentVideo"
                                    className="videochat-video"
                                    poster='/assets/img/video-call-icon-on-white.jpg'
                                    style={{
                                        width: "100%",
                                        height: "100%",
                                        objectFit: "contain"
                                    }}
                                ></video>
                            </div>
                        </Col>
                    </Row>
                </div>
            </Col>
        </Row>
    );
}

export default DisplayVideoChat;



// {/*
//                                 If user is instructor, then display instructor video first or
//                                 if user is student, then display student video first
//                             */}
//                             <div className='local-video-block'>
//                                 <video
//                                     ref={localVideoRef}
//                                     id="localVideo"
//                                     autoPlay={videoAutoPlay}
//                                     className='local-video'
//                                     playsInline={true}
//                                     style={{
//                                         width: "140px",
//                                         height: "100px",
//                                     }} />
//                             </div>

//                             {/*
//                                 If user is instructor, then display student video second
//                                 or if user is student, then display instructor video second

//                             */}
//                             <div className="remote-video-block">
//                                 <video
//                                     ref={remoteVideoRef}
//                                     poster="/assets/img/vecteezy_video-call-icon-vector-flat-design-isolated-on-white_11020194.jpg"
//                                     id="remoteVideo"
//                                     playsInline={true}
//                                     autoPlay={videoAutoPlay}
//                                     className='remote-video' />
//                             </div>
