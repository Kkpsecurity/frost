import React from "react";

const AllowAccessSupportText = () => {
    return (
        <div>
            <p>
                Understanding Student Presence Tracking and Lesson Participation
            </p>
            <h4>Overview:</h4>
            <p>
                Our platform uses a polling mechanism to manage and track
                student presence during class sessions. This system plays a
                crucial role in ensuring students actively participate in
                lessons and adhere to the course requirements.
            </p>
            <h3>How It Works:</h3>
            <p>
                <ul>
                    <li>
                        At Lesson Start: When a lesson begins, the system
                        automatically checks for the student's presence on the
                        class page.
                    </li>
                    <li>
                        Active Presence: If a student is on the page at the
                        start of the lesson, a record is created to indicate the
                        student was present, allowing them to continue
                        participating in the class.
                    </li>
                    <li>
                        {" "}
                        Absent Presence: If the student is not on the page at
                        the lesson's start and fails to return within 5 minutes,
                        no record of presence is created for that lesson. This
                        absence may occur if the student is browsing other sites
                        during this critical time.
                    </li>
                    <li>
                        Being Kicked Off: Currently, there is no feature that
                        automatically kicks a student out after a lesson has
                        started. If a student is kicked out, it's because they
                        were not present on the page at the lesson's start.
                        There is also a grace period of 5 minutes to account for
                        this.
                    </li>
                </ul>
            </p>
            <p>
                <b>Implications:</b> Missing Lessons: Students not present on
                the page at the lesson start and absent for more than 5 minutes
                are considered to have missed the lesson. This absence impacts
                their attendance record and may require them to catch up on
                missed material. Support Team's Role: It's essential for the
                support team to understand this mechanism to address student
                queries accurately. Students questioning their attendance
                records or experiencing issues with lesson access should be
                informed about the importance of being on the class page at the
                start of each lesson. Communicating to Students: Proactive
                Communication: Encourage students to remain on the class page
                before and during lessons to ensure their attendance is
                accurately recorded. Technical Issues: Advise students to
                contact support immediately if they face technical difficulties
                that prevent them from being on the class page at the start of a
                lesson.
            </p>
        </div>
    );
};

export default AllowAccessSupportText;
