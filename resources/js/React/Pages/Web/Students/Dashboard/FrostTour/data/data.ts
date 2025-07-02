const LiveClassHelpData = [
    {
        selector: ".student-navbar-container",
        title: "The Title Bar",
        description:
            "The Title Bar displays the name of the current classroom along with any dynamically generated links throughout your course. As you approach the end of the course, a 'Take Exam' button will also appear here for your convenience.",
        position: "bottom:right",
        width: 480,
        height: 280,
    },
    {
        selector: ".frost-sidebar",
        title: "The Lesson Sidebar",
        description:
            "The Lesson Sidebar showcases your lessons for the day. Each lesson is color-coded to indicate its status: Green signifies a completed lesson, Yellow points to an incomplete one, Red indicates a failed lesson, and Blue highlights the lesson you're currently viewing or working on.",
        position: "top:right",
        width: 320,
        height: 480,
    },
    {
        selector: ".frost-player-container",
        title: "Screen Sharing Player",
        description:
            "The Screen Sharing Player, built on Zoom's interface, provides a live stream of both the instructor's screen sharing content and video. It's crucial to stay engaged and follow your instructor's lead to fully grasp the content. If you encounter connection issues or webcam problems, please refer to the troubleshooting section directly below the player.",
        position: "top:right",
        width: 480,
        height: 280,
    },
    {
        selector: ".frost-player-meta",
        title: "Screen Sharing Player Details",
        description:
            "This section provides an overview of the course. It displays the total lessons scheduled for the day, tracks your progress by indicating lessons completed, and gives an overall count of all the lessons in the course, highlighting how many you've finished.",
        position: "top::center",
        width: 480,
        height: 280,
    },
    {
        selector: ".frost-session-reminder",
        title: "Session Reminder",
        description:
            "One Important factor on taking the class is understanding the way we manage session, Please make sure you read the session reminder before taking the class.",
        position: "top:right",
        width: 480,
        height: 280,
    },
    {
        selector: ".frost-webcam-container",
        title: "Webcam Player Information",
        description:
            "The Webcam can in some cases can experience issues, in most cases you can reset your webcam to resolvew the issue make suire your read this section ofr help.",
        position: "bottom:center",
        width: 480,
        height: 280,
    },
    {
        selector: ".instrcutor-card",
        title: "Your Instructor",
        description:
            "This section showcases your instructor. Please note that the instructor's primary role is to provide course explanations, not technical support. If you need assistance or support, refer to our support center located at the bottom right corner of the webpage.",
        position: "top:left",
        width: 480,
        height: 280,
    },
    {
        selector: ".chat-card",
        title: "Chat Room",
        description:
            "The Chat Room allows for direct communication with your instructor. However, it's vital to understand that the instructor addresses only course-related queries. For technical assistance or other support, please use our support center situated at the bottom right corner of the webpage.",
        position: "center:left",
        width: 480,
        height: 280,
    },
    {
        selector: ".active-lesson-card",
        title: "Current Lesson",
        description:
            "The 'Current Lesson' section showcases the lesson you're presently on, providing an estimated progress bar for that specific lesson. Additionally, it offers updates and messages related to your overall course progression.",
        poisiton: "top:left",
        width: 480,
        height: 280,
    },
    {
        selector: ".image-preview-card",
        title: "Identity Image Preview",
        description:
            "This section showcases the photos you've uploaded for identity verification. Rest assured, only you and the instructor can view these images; they are not accessible to other students.",
        position: "top:left",
        width: 480,
        height: 280,
    },
    {
        selector: ".document-card",
        title: "Essential Documents",
        description:
            "All necessary documentation for the course can be found here. While supplemental materials related to the course are housed in the offline classroom, key documents vital for in-session requirements will be displayed in this section.",
        position: "top:left",
        width: 480,
        height: 280,
    },
];

const OfflineClassHelpData = [
    {
        selector: ".student-navbar-container",
        title: "Title Bar",
        description:
            "The Title Bar displays the name of the current classroom along with any dynamically generated links throughout your course. As you approach the end of the course, a 'Take Exam' button will also appear here for your convenience.",
        position: "bottom:right",
        width: 480,
        height: 280,
    },
    {
        selector: ".frost-sidebar",
        title: "Lesson Sidebar",
        description:
            "This lesson is a list of all lessons that pretain to the course. you can click on the elsson to review that section related to that lesson ",
        position: "top:right",
        width: 320,
        height: 480,
    },
    {
        selector: ".frost-content-header-nav",
        title: "Offline Navigation Bar",
        description:
            "The Offline Navigation Bar facilitates navigation between various sections of the course or lesson. It provides quick access to the Dashboard, associated Videos, Documents, and other relevant components.",
        position: "bottom:center",
        width: 480,
        height: 280,
    },    
    {
        selector: ".home-nav-tab",
        title: "Home Link",
        description:
            "The Home Navigation Link redirects you to the homepage of the Offline Dashbaord. here is an overview of the course and the lessons that are available to you.",
        position: "bottom:center",
        width: 480,
        height: 280,
    },
    {
        selector: ".videos-nav-tab",
        title: "Video Link",
        description:
            "The Video Navigation Link serves as your portal to the comprehensive video library within the Offline Dashboard. It provides convenient access to a curated selection of educational content, allowing you to revisit and review videos previously presented in the classroom. This feature is particularly beneficial for reinforcing learning or catching up on missed material, ensuring a continuous and flexible learning experience.",
        position: "bottom:center",
        width: 480,
        height: 280,
    },
    {
        selector: ".documents-nav-tab",
        title: "Documents Link",
        description:
            "The Documents Navigation Link redirects you to the documents section of the Offline Dashboard. Here you can view all the documents that are available to you.",
        position: "bottom:center",
        width: 480,
        height: 280,
    },
    {
        selector: ".course-detail-card",
        title: "Course Details",
        description:
            "The Course Details section provides a brief overview of the course, including the course name, instructor, and the number of lessons in the course.",
        position: "bottom:right",
        width: 480,
        height: 280,
    },
    {
        selector: ".student-info-card",
        title: "Student Information",
        description:
            "The Student Information section provides a brief overview of the student, including the student's name, email address, and other student information.",
        position: "top-right",
        width: 480,
        height: 280,
    },
    {
        selector: ".lesson-completed-card",
        title: "Lesson Completion",
        description:
            "The Lesson Completion section provides a brief overview of the lessons completed, lessons remaining, and the total number of lessons in the course.",
        position: "top-right",
        width: 480,
        height: 280,
    },
];

export { LiveClassHelpData, OfflineClassHelpData };
