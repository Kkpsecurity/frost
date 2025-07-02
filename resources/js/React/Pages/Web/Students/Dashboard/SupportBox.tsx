import React, { useState } from "react";
import styled from "styled-components";
import { Button, Container } from "react-bootstrap";

const SupportContainer = styled(Container)`
    background-color: #f7f7f7;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
`;

const TitleContainer = styled.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
`;

const RevealButton = styled.button`
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 5px;
    cursor: pointer;
    &:hover {
        background-color: #0056b3;
    }
`;

const SupportLink = styled(Button)`
    margin-right: 10px;
    margin-bottom: 10px;
    &.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
`;

const MessageContainer = styled.div`
    padding: 15px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    transition: box-shadow 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
`;


const SupportBox = () => {
    const [selectedBrowser, setSelectedBrowser] = useState(null);
    const [showContent, setShowContent] = useState(false);

    const supportGuide = {
        title: 'Webcam Troubleshooting Guide',
        menuItem: 'Webcam Troubleshooting',
        subTitle: 'Resolving Webcam Connection Issues',
        desc: 'Different browsers may present unique challenges when it comes to webcam functionality. However, most browsers offer a reset feature for the webcam, which typically resolves about 80% of connectivity issues. In this guide, we\'ll walk you through the steps to reset your webcam settings when encountering difficulties with the screen share interface.',
        browsers: {
            "firefox": {
                title: 'Firefox',
                supportText: 'To reset your webcam settings in Firefox, click on the camera icon located in the address bar. From the dropdown, select the option to "Always Allow" access to your webcam.'
            },
            "chrome": {
                title: 'Chrome',
                supportText: 'In Chrome, you can reset your webcam settings by clicking on the lock icon situated to the left of the address bar. Here, you\'ll find both the camera and microphone icons. After adjusting your settings, remember to refresh the page for changes to take effect.'
            },
            "safari": {
                title: 'Safari',
                supportText: `Navigate to the website you want to manage permissions for.
                Click on Safari in the menu bar, then select “Settings for this website”.
                In the dropdown that appears, you’ll see settings for both the camera and microphone.
                Change access to “Allow” for both the camera and microphone.
                Refresh the page for changes to take effect.`
            },            
            "edge": {
                title: 'Edge',
                supportText: 'For Edge users, click on the lock icon in the address bar, then navigate to "Permissions for this site." Here, you\'ll find options to reset your camera settings.'
            },
            "ie": {
                title: 'Internet Explorer',
                supportText: `Open Internet Explorer.
                Click on the gear icon in the top right corner, then select “Internet options”.
                Go to the “Privacy” tab.
                Under “Settings”, click on “Advanced”.
                In the “Advanced Privacy Settings” window, under “Permissions”, you can manage which websites have permission to access your webcam.`
            }
            
        }
    };
        
    const handleBrowserClick = (browser) => {
        setSelectedBrowser(browser);
    };

    return (
        <SupportContainer>
            <TitleContainer>
                <h4>{supportGuide.title}</h4>
                <RevealButton onClick={() => setShowContent(prev => !prev)}>
                    {showContent ? <i className="fa fa-arrow-up"></i> : <i className="fa fa-arrow-down"></i>}
                </RevealButton>
            </TitleContainer>
    
            {showContent && (
                <>
                    <p>{supportGuide.desc}</p>
    
                    {Object.keys(supportGuide.browsers).map(browser => (
                        <SupportLink 
                            key={browser} 
                            variant={selectedBrowser === browser ? "primary" : "outline-primary"} 
                            onClick={() => handleBrowserClick(browser)}
                            className={selectedBrowser === browser ? "active" : ""}
                        >
                            {supportGuide.browsers[browser].title}
                        </SupportLink>
                    ))}
    
                    {selectedBrowser && (
                        <MessageContainer>
                            <p>{supportGuide.browsers[selectedBrowser].supportText}</p>                            
                        </MessageContainer>
                    )}
                </>
            )}
        </SupportContainer>
    );
    
};

export default SupportBox;
