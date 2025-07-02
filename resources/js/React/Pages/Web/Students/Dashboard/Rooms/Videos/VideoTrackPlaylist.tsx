import React from "react";
import { ListGroup } from "react-bootstrap";

const VideoTrackPlaylist = () => {
    return (
        <div className="d-none d-md-block">
            <h5>Playlist</h5>
            <ListGroup>
                <ListGroup.Item action>
                    <div className="d-flex align-items-center">
                        <img
                            src="https://via.placeholder.com/80x60.png?text=Video+1"
                            alt="Video 1"
                            width="80"
                            height="60"
                            className="mr-3"
                        />
                        <div className="p-3">
                            <h6>Video 1</h6>
                            <p className="mb-0">January 1, 2022</p>
                        </div>
                    </div>
                </ListGroup.Item>
                <ListGroup.Item action>
                    <div className="d-flex align-items-center">
                        <img
                            src="https://via.placeholder.com/80x60.png?text=Video+2"
                            alt="Video 2"
                            width="80"
                            height="60"
                            className="mr-3"
                        />
                        <div className="p-3">
                            <h6>Video 2</h6>
                            <p className="mb-0">February 14, 2022</p>
                        </div>
                    </div>
                </ListGroup.Item>
                <ListGroup.Item action>
                    <div className="d-flex align-items-center">
                        <img
                            src="https://via.placeholder.com/80x60.png?text=Video+3"
                            alt="Video 3"
                            width="80"
                            height="60"
                            className="mr-3"
                        />
                        <div className="p-3">
                            <h6>Video 3</h6>
                            <p className="mb-0">March 31, 2022</p>
                        </div>
                    </div>
                </ListGroup.Item>
            </ListGroup>
        </div>
    );
};

export default VideoTrackPlaylist;
