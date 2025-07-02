import React from "react";
import styled from "styled-components";

// Define global CSS variables
const FrostVideoRoot = styled.div`
  :root {
    --video-chat-container-width: 100%;
    --video-chat-container-height: 280px;
    --video-chat-container-background-color: #424242;
  }
`;

// Define component-specific styles
const FrostVideoTheme = styled.section`
  display: block;
  width: 100%;
  height: 100%;
  background-color: #424242;
  color: #fff;
  padding: 0;
  margin: 0;
  font-family: "Roboto", sans-serif;
  font-size: 14px;
  line-height: 1.5;
  overflow: hidden;
  transition: all 0.3s ease-in-out;
  transition-property: background-color, color;
  
  .videochat-container {
    width: var(--video-chat-container-width);
    min-height: var(--video-chat-container-height);
    height: var(--video-chat-container-height);
    background-color: var(--video-chat-container-background-color);
    display: flex;
  }

  .caller-video,
  .receiver-video {
    min-height: var(--video-chat-container-height);
    height: var(--video-chat-container-height);
    width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .caller-video video,
  .receiver-video video {
    min-height: var(--video-chat-container-height);
    height: var(--video-chat-container-height);
    max-width: 100%;
  }

  .receiver-video {
    overflow-y: scroll;
  }

  .student-info {
    display: flex;
    align-items: center;
  }

  .student-name {
    margin-left: 10px;
  }
  
  .nav-tabs {
    border: none;
  }

  .tab-content {
    height: 100%;
    overflow-y: scroll;
    background: #787878;
    min-height: var(--video-chat-container-height);
    height: var(--video-chat-container-height);
    .list-group {
      height: var(--video-chat-container-height);
    }
  }
`;

// Export both components
export { FrostVideoRoot, FrostVideoTheme };
