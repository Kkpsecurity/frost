import React, { useRef, useState } from "react";

const VideoPlayer = ({lesson_id}) => {
  const videoRef = useRef(null);
  const [playing, setPlaying] = useState(false);
  const [volume, setVolume] = useState(1);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);

  const handlePlay = () => {
    videoRef.current.play();
    setPlaying(true);
  };

  const handlePause = () => {
    videoRef.current.pause();
    setPlaying(false);
  };

  const handleVolumeChange = (event) => {
    const value = parseFloat(event.target.value);
    videoRef.current.volume = value;
    setVolume(value);
  };

  const handleTimeUpdate = () => {
    setCurrentTime(videoRef.current.currentTime);
    setDuration(videoRef.current.duration);
  };

  const handleSkipBackward = () => {
    videoRef.current.currentTime -= 10;
  };

  const handleSkipForward = () => {
    videoRef.current.currentTime += 10;
  };

  return (
    <div className="container"   style={{
      marginTop: '1rem',
      marginBottom: '1rem',
      padding: '1rem',
      backgroundColor: '#fff',
      borderRadius: '5px',
      boxShadow: '0 0 10px rgba(0,0,0,0.2)'
    }}>
      <video
        width="100%"
        height="100%"
        ref={videoRef}
        onTimeUpdate={handleTimeUpdate}
        onEnded={handlePause}
        className="video-player"
        style={{
          height: "420px",
          width: "100%",
          objectFit: "cover",
        }}
      >
        <source
          src="https://www.learningcontainer.com/wp-content/uploads/2020/05/sample-mp4-file.mp4"
          type="video/mp4"
        />
        Your browser does not support the video tag.
      </video>
      <div className="video-controls">

        <button onClick={playing ? handlePause : handlePlay}>
          <i className={`fas fa-${playing ? 'pause' : 'play'}`}></i>
        </button>

        <input
          type="range"
          min="0"
          max="1"
          step="0.1"
          value={volume}
          onChange={handleVolumeChange}
        />

        <button onClick={handleSkipBackward}>
          <i className="fas fa-backward"></i>
        </button>

        <button onClick={handleSkipForward}>
          <i className="fas fa-forward"></i>
        </button>

      </div>
    </div>
  );
};

export default VideoPlayer;
