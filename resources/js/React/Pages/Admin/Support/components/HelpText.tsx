import React from "react";
import { Accordion, Card, useAccordionButton } from "react-bootstrap";

const supportDocs = [
    {
        title: "Welcome to Support Docs",
        content:
            "Welcome to the 'Support Docs' help section, a comprehensive guide designed specifically for our dedicated support staff and instructors. This resource is crafted to help you with the necessary knowledge and tools to effectively manage and support our diverse student body, whether they are engaging with us online or offline classrooms.",
    },
    {
        title: "Introduction to Data Flow",
        content:
            "Understanding the flow of data is crucial in managing and supporting our students' educational journey. The data associated with student courses is categorized into two main types:\n\n- Dynamic Data: This encompasses the information that can identify a student and assist in managing their account. Dynamic data is pivotal for personalizing the student's learning experience, tracking progress, and facilitating administrative tasks.\n\n- Real-time Data: Available exclusively during online classes, real-time data provides immediate insights into the student's engagement, performance, and interaction with the course material. This type of data is instrumental in offering timely support, making adjustments to teaching strategies, and enhancing the overall learning environment.\n\nBy understanding and utilizing these data types effectively, support staff and instructors can significantly improve the efficiency of their support mechanisms, ensuring that students receive the guidance they need, when they need it.",
    },
    {
        title: "Accessing the Support Section",
        content:
            "To access the support center, navigate to the sidebar menu and select 'Front Support.' This action will direct you to the support section, where you will be welcomed by a search box.\n\nIn this area, you can search for the student in question either by their name or email. The search result will display a list of students related to your search term, where you can select the student.",
    },
    {
        title: "The Student Dashboard",
        content:
            "The dashboard is designed to provide detailed insights into a student's current status within the system, regardless of whether they are participating in offline or online activities. The primary components of the dashboard are outlined below:\n\n- Student Profile Panel: At the top, the dashboard displays the student card, identifying the individual you are currently assisting. This panel serves as a quick reference to who the student is, including their name and other relevant details.\n\n- Attendance Record: Directly beneath the student profile panel, there's a section dedicated to showing the student's attendance record for the current week. This area is crucial for monitoring participation and identifying any patterns or issues in attendance.\n\n- Main Area with Tab System: The central part of the dashboard employs a tab system to organize various types of information. This design allows for efficient access to different data sets, such as academic performance, course enrollments, communication logs, and more, enabling support staff to have a holistic view of the student's engagement and progress.",
    },
    {
        title: "Student Profile Panel",
        content:
            "At the top, the dashboard displays the student card, identifying the individual you are currently assisting. This panel serves as a quick reference to who the student is, including their name and other relevant details.",
    },
    {
        title: "Attendance Record",
        content:
            "Directly beneath the student profile panel, there's a section dedicated to showing the student's attendance record for the current week. This area is crucial for monitoring participation and identifying any patterns or issues in attendance.",
    },
    {
        title: "Main Area with Tab System",
        content:
            "The central part of the dashboard employs a tab system to organize various types of information. This design allows for efficient access to different data sets, such as academic performance, course enrollments, communication logs, and more, enabling support staff to have a holistic view of the student's engagement and progress.",
    },
    {
        title: "Main Area with Tab System",
        content: "The central part of the dashboard employs a tab system to organize various types of information. This design allows for efficient access to different data sets, such as academic performance, course enrollments, communication logs, and more, enabling support staff to have a holistic view of the student's engagement and progress."
    },
    {
        title: "Tab 1: Activity",
        content: "To view a student's activity, you must first select a course they are enrolled in. This selection is made through a dropdown box. Once a course is selected, the activities related to it are listed below. This includes information on the course lifecycle, such as purchase date, start date, and completion status, providing a quick overview of the student's progress through the course."
    },
    {
        title: "Tab 2: Lessons",
        content: "This tab displays a list of all lessons associated with the selected course, along with the student's progress status for each lesson. It indicates whether a lesson is pending, completed, or incomplete, offering a detailed view of the student's engagement with the course material."
    },
    {
        title: "Tab 3: Attendance",
        content: "Here, you'll find a list of days the student has attended, accompanied by the status for each day (completed, failed, etc.). Clicking on a status provides access to the lesson for that day, detailing the lesson completed and the challenges associated with it. This allows for a comprehensive view of what was covered each day and which challenges the student encountered or missed."
    },
    {
        title: "Tab 4: Student Photos",
        content: "This tab facilitates the management of student identification photos. It allows for the validation or rejection of submitted photos. If a photo is declined, the student is prompted to re-upload a new image. Currently, there are no penalties for not having photos, but this tab ensures that student identities are verified and managed effectively."
    },
    {
        title: "Tab 5: Student Details",
        content: "Details about this tab are not provided in the input, but you can assume it contains specific information related to the student's personal and academic details, facilitating a comprehensive understanding of their background and current status within the educational program."
    },
    {
        title: "The Status Bar and the Toolbar",
        content: "The Status Bar is designed to display the current status of the student, while the Toolbar provides the necessary tools for effectively managing that status. Key functionalities include:\n\n- **Allow Access**: Activated when a student lacks access to a lesson, typically due to not being focused on the page during a lesson change. Handling of this scenario is at the support staff's discretion.\n\n- **DNC (Did Not Complete)**: This status is assigned if the student misses two challenges or is not focused during the lesson change at the end of a lesson. It serves as an alert to both students and instructors about incomplete coursework.\n\n- **Ban Student**: This irreversible action can only be undone by a system administrator. It's a measure of last resort for severe cases, reflecting the serious implications for a student's ability to access the course."
    },
    {
        title: "Apple Products and The Student Interface",
        content: "We've encountered an issue where the player was not functioning properly on Apple devices. A practical solution is for students to download the Zoom player specifically designed for their device. While the browser version offers support on some devices, downloading the application can help avoid issues related to outdated drivers.\n\nUnderstanding the Student Interface is crucial for efficient assistance. The UI is tailored to provide students with essential information needed to progress through their course seamlessly. It incorporates troubleshooting features like webcam reset and alerts to prevent navigation away from active lessons. Moreover, it includes a progress tracker detailing the number of lessons completed and those remaining, ensuring students are well-informed of their progress."
    },
    {
        title: "Beta Mode",
        content: "Currently, the system is considered to be in beta mode, indicating that users may encounter certain issues as we continue to refine its performance and capabilities. This stage is crucial for identifying and resolving potential problems, ensuring a smooth transition to full operation."
    },
    {
        title: "Variables: Undefined",
        content: "Due to the complex nature of the system, whether offline or online, the status of variables can be dynamic. It's plausible that you might encounter an undefined variable during your interaction with the system. This is a known possibility given the system's current development stage, and we're actively working to minimize such occurrences."
    },
    {
        title: "Data Sync Problems",
        content: "One of the challenges we face involves data synchronization. As we strive to emulate a real-time environment, some data may become stale or fail to update if synchronization processes are not perfectly aligned. If you observe data that seems like it should be updating but isn't, please report this issue to ensure timely resolution."
    },
    {
        title: "Reporting Bugs",
        content: "To report bugs, continue to use email as the primary method of communication. When reporting, please include the following information:\n\n- Date: The date on which you encountered the issue.\n- Issue Description: A detailed explanation of the problem. If there are any error messages, please copy and paste the text into the email or include a screenshot."
    },
    {
        title: "Support Team Views",
        content: "You are the one working with this system, so it is imperative that you provide feedback to help make your job easier. As you perform your support tasks, jot down any improvements, updates, and features you think that will enhance your process and the overall system efficiency."
    },
    {
        title: "Pushing and Updates",
        content: "Pushing is the term we use when updating the main site. Any fixes or updates will be addressed during a Push. All pushes are performed on Thursdays to ensure the best outcome. This means if there are any complaints, they may not be fixed until the following Thursday, unless an emergency demands immediate action."
    }
    
];



const HelpText = () => {
    const Toggle = ({ children, eventKey }) => {
        const decoratedOnClick = useAccordionButton(eventKey);

        return (
            <button
                type="button"
                style={{ backgroundColor: "transparent", border: "none" }}
                onClick={decoratedOnClick}
            >
                {children}
            </button>
        );
    };

    return (
        <Accordion defaultActiveKey="0">
            {supportDocs.map((doc, index) => (
                <Card key={index}>
                    <Card.Header>
                        <Toggle eventKey={`${index}`}>{doc.title}</Toggle>
                    </Card.Header>
                    <Accordion.Collapse eventKey={`${index}`}>
                        <Card.Body>{doc.content}</Card.Body>
                    </Accordion.Collapse>
                </Card>
            ))}
        </Accordion>
    );
};

export default HelpText;
