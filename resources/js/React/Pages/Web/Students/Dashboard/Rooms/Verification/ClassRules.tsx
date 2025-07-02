import React, { useEffect } from "react";

// Load the sound file
const challengeSound = window.location.origin + "/assets/sound/challenge.mp3";
const audio = new Audio(challengeSound);

// import { rules } from "../../../Partials/onBoarding";

const ClassRules = ({ onAgree }, ...props) => {
    const handleAgreement = () => {
        // scrool to top
        window.scrollTo(0, 0);
        onAgree();
    }

    const rules = {};

    // Function to play the sound
    const playSound = () => {
        audio.play();
    };

    useEffect(() => {      
        // Cleanup the audio
        return () => {
            audio.pause();
            audio.currentTime = 0;
        };
    }, []);

    return (
        <div className="container p-5 mt-4">
            <h1>Challenges and Attendance</h1>
            <p>Understanding the Classroom Procedures</p>
            <ul className="list-group">
                <li className="list-group-item">
                    <h4>How is the student checked into the class?</h4>
                    <p style={{ fontSize: "14px" }}>
                        Students are expected to join the class by the scheduled
                        start time. Attendance is officially recorded when the
                        instructor begins the day's session. Arriving late will
                        still result in being marked as present, but please note
                        that there is a cutoff time. If you arrive after this
                        cutoff, you may be barred from participating in the
                        first lesson of the day.
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>Why am I seeing Lesson in Progress?</h4>
                    <p style={{ fontSize: "14px" }}>
                        If you come in late or if you are not focused on the
                        class, during lesson changes, you can be blocked from
                        taking that lesson. In this case, you must wait until
                        the next lesson to continue the course.
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>
                        If I am on the Lesson in Progress screen, can I rejoin
                        the class?
                    </h4>
                    <p style={{ fontSize: "14px" }}>
                        Yes, if you find yourself on the Lesson in Progress
                        screen, it is possible to rejoin the lesson. You should
                        promptly contact your support to request re-entry. This
                        request will then be reviewed by either the support team
                        or your instructor before a decision is made.
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>What are Lesson Challenges?</h4>
                    <p style={{ fontSize: "14px" }}>
                        Lesson Challenges are interactive checks designed to
                        verify student engagement during the lesson. Similar to
                        a captcha box, these challenges involve a prompt where
                        you're required to enter a 4-digit code displayed on the
                        screen. This code ensures that you are actively
                        participating and focused on the lesson material. These
                        challenges occur periodically throughout the lesson and
                        only during active lesson periods. Failing to respond
                        correctly to the challenge may indicate a lack of
                        attention or absence from the lesson, affecting your
                        participation record for that session.
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>Challenge Alerts</h4>
                    <p style={{ fontSize: "14px" }}>
                        Students will be notified with an alert when a challenge
                        is about to commence. For those in fullscreen mode,
                        particularly during a screenshare, an audio cue will be
                        provided. If you hear this sound, it's important to exit
                        fullscreen mode promptly to engage with and respond to
                        the challenge.{" "}
                        <a href="#" onClick={playSound}>
                            <i className="fa fa-volume-up"></i> Test Sound
                        </a>
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>
                        What happens if the student does not complete the
                        challenge?
                    </h4>
                    <p style={{ fontSize: "14px" }}>
                        Failing to respond to two challenges within a single
                        lesson results in the lesson being marked as incomplete.
                        However, it's important to remember that failing a
                        lesson doesn't equate to failing the entire day. You
                        have the opportunity to retake lessons, either by
                        participating in an offline class or by returning on a
                        subsequent day to redo the lesson.
                    </p>
                </li>
                <li className="list-group-item">
                    <h4>
                        What if I have a valid reason for missing a challenge?
                    </h4>
                    <p style={{ fontSize: "14px" }}>
                        Re-admission to class or having an incomplete reversed
                        is at the instructor's discretion and requires a valid
                        reason for missing the challenge. Note: this step must
                        be reviewed first and can take up to 24 hrs for a
                        reversal.
                    </p>
                </li>
            </ul>
            <div className="mt-3">
                <button
                    className="btn btn-primary btn-lg"
                    onClick={handleAgreement}
                >
                    I understand the Rules
                </button>
            </div>
        </div>
    );
};

export default ClassRules;
