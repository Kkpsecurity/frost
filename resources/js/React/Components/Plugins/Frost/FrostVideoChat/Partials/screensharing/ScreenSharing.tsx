import React, { useState } from "react";
import { AgoraVideoPlayer } from "agora-rtc-react";
import { useScreenShareClient, screenShareConfig } from "../../sharing_settings";
import axios from "axios";
import AgoraRTC from "agora-rtc-sdk";

type ScreenSharingProps = {};

const ScreenSharing: React.FC<ScreenSharingProps> = () => {
  const [tracks, setTracks] = useState<any>(null);
  const [screenShareClient, setScreenShareClient] = useState<any>(null);

  // Create a new Agora client object
  const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

  // Create a new Agora stream object with screen sharing enabled
  const stream = AgoraRTC.createStream({
    streamID: "screen",
    audio: false,
    video: false,
    screen: true,
    mediaSource: "screen",
  });

  const startScreenSharing = async () => {
    try {
      // Generate a token for the client
      const response = await axios.get(
        `http://localhost:8000/getToken?channelName=screen-share&role=publisher`
      );
      const token = response.data.token;

      // Join the channel using the token and channel name
      await client.join(token, screenShareConfig.channelName, null, null);

      // Initialize the Agora stream
      await stream.init();

      // Publish the stream to the channel
      await client.publish(stream);

      // Set the tracks state to the screen track
      setTracks(stream);

      console.log("Connected", client);
      return client;
    } catch (error) {
      console.error("Failed to start screen sharing", error);
      return null;
    }
  };

  return (
    <div className="container">
      <div className="row">
        <div className="col-2">
          <button
            className="btn btn-success circle"
            onClick={startScreenSharing}
            aria-label="Start screen sharing"
          >
            <i className="fas fa-desktop" />
          </button>
        </div>
        <div
          className="col-2"
          style={{
            width: "100px",
            height: "60px",
            background: "#333",
          }}
        >
          {tracks && (
            <AgoraVideoPlayer
              videoTrack={tracks}
              style={{
                width: "100px",
                height: "60px",
              }}
            />
          )}
        </div>
        <div className="col-8">Tools</div>
      </div>
    </div>
  );
};

export default ScreenSharing;
