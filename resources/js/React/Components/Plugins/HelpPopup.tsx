import React, { useEffect, useState } from "react";
import "./PopHelp.css";

interface HelpPopupProps {
  title: string;
  content: string;
  openAfter?: number;
  children: React.ReactNode;
}

/**
 *
 * @param param0
 * @returns
 */
const HelpPopup: React.FC<HelpPopupProps> = ({
  title,
  content,
  openAfter = 5000,
  children,
}) => {
  const [showPopup, setShowPopup] = useState<boolean>(false);

  useEffect(() => {
    if (openAfter) {
      setTimeout(() => {
        setShowPopup(true);
      }, openAfter);
    }
  }, []);

  return (
    <>
        {children}
        // if showPopup is true, then show the popup
        showPopup === true ? (
            <div className="popHelp__overlay"></div>
            <div className="popHelp">
                <div className="popHelp__content">
                    <h3>{title}</h3>
                    <p>{content}</p>
                    <button onClick={() => setShowPopup(false)}>Close</button>
                </div>
            </div>
        ) : null
    </>
  );
};

export default HelpPopup;

// use like this
// <HelpPopup title={data.course.title} content={data.course.title_long} >
// <ZoomMeetingInterface
//    data={data}
//    course_date_id={course_date_id}
//    openMeetingSession={openMeetingSession}
// />
// </HelpPopup>
