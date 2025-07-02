import styled from "styled-components";

export const VideoControlsContainer = styled.div`
  position: relcative;
  left: 0;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.5rem;
  z-index: 30;
  transition: bottom 0.5s ease-in-out;

  .video-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0.5rem;
  }

  .video-controls button {
    background-color: transparent;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    margin: 0 0.5rem;
    cursor: pointer;
  }

  .video-controls button:hover {
    color: #ccc;
  }

  .video-controls button:disabled {
    color: #ccc;
    cursor: not-allowed;
  }
`;

export const VideoPlayerContainer = styled.div`
  position: relative;

  &:hover .video-controls {
    bottom: 0;
    display: flex;
    max-height: 320px;
    height: 100%;
    transition: bottom 0.5s ease-in-out;
  }
`;

export const RemoteVideoPlayer = styled.video`
  background-color: #777;
  width: 100%;
  max-width: 100%;
  height: 260px;
  padding: 0;
  margin: 0;
  position: relative;
  object-fit: cover;
  z-index: 1;
`;

export const LocalVideoPlayer = styled.video`
  background-color: #888;
  width: 120px;
  height: 90px;
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 2;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  border: 1px solid #ccc;
`;

// Media Queries
const breakpoints = {
  small: "480px",
  medium: "768px",
  large: "992px",
};

export const media = {
  small: `(max-width: ${breakpoints.small})`,
  medium: `(max-width: ${breakpoints.medium})`,
  large: `(max-width: ${breakpoints.large})`,
};

// Responsive Styles
export const ResponsiveVideoControlsContainer = styled(VideoControlsContainer)`
  @media ${media.small} {
    padding: 0.25rem;
  }
`;

export const ResponsiveVideoPlayerContainer = styled(VideoPlayerContainer)`
  @media ${media.small} {
    &:hover .video-controls {
      bottom: 0;
    }
  }
`;

export const ResponsiveLocalVideoPlayer = styled(LocalVideoPlayer)`
  @media ${media.small} {
    width: 80px;
    height: 60px;
    top: 5px;
    left: 5px;
  }
`;
