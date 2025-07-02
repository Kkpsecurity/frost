import axios from "axios";
import AgoraRTC from "agora-rtc-sdk";

/**
 * Agora App ID
 */ 
const AgoraAppID = "106f437cc9614c8880420300acf86c7b";
const screenShareChannelName = "screen-share";
const role = "publisher";
const tokenServerUrl = "http://localhost:8000";

// Create a new Agora client object
const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

// Create a new Agora stream object with screen sharing enabled
const stream = AgoraRTC.createStream({
  streamID: "screen",
  audio: false,
  video: false,
  screen: true,
  mediaSource: "screen"
});

// Generate a token for the client
async function generateToken() {
  const response = await axios.get(
    `${tokenServerUrl}/getToken?channelName=${screenShareChannelName}&role=${role}`
  );

  const token = response.data.token;
  return token;
}

// Initialize the client and stream
async function initialize() {
  try {
    // Initialize the Agora client
    await client.init(AgoraAppID);

    // Generate a token for the client
    const token = await generateToken();

    // Join the channel using the token and channel name
    await client.join(token, screenShareChannelName, null, null);

    // Initialize the Agora stream
    await stream.init();

    // Publish the stream to the channel
    await client.publish(stream);

    console.log("Screen sharing initialized successfully");
  } catch (err) {
    console.error("Failed to initialize screen sharing", err);
  }
}

// Call the initialize function to initialize the client and stream
initialize();
