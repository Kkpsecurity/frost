const colorConfig = {
    dangerItem: "#ff4d4f",
    warningItem: "#faad14",
    primaryItem: "#1890ff",
    defaultItem: "#d9d9d9",
    infoItem: "#13c2c2"
};

type StudentMessageType = {
    message: string;
    detail: string;
    bgColor: string;
};

type StudentMessagesType = {
    [key: string]: StudentMessageType;
};

const progressMessages: StudentMessagesType = {
    AgreementNotSigned: {
        message: "Not Agreed to Terms",
        detail: "The student has not agreed to the terms and conditions of the course.",
        bgColor: colorConfig.warningItem,
    },
    ClassRoomRules: {
        message: "Classroom Rules Not Accepted",
        detail: "The student has not accepted the classroom rules.",
        bgColor: colorConfig.warningItem,
    },
    PhotosMissing: {
        message: "Validation Photos Not Uploaded",
        detail: "The student has not submitted one or more of their ID verification photos.",       
        bgColor: colorConfig.warningItem,
    },
    IDNotValidated: {
        message: "Pending ID Validation",
        detail: "The student has uploaded their ID photos and is ready for validation.",
        bgColor: colorConfig.dangerItem,
    },
    CheckedIn: {
        message: "Ready for Class",
        detail: "The student is ready for class; however, this is not a validated student.",
        bgColor: colorConfig.defaultItem,
    },
};

const statusMessages: StudentMessagesType = {
    Banned: {
        message: "Banned",
        detail: "The student has been disabled from participating in the course and will receive a failing grade. Only SysAdmin can Revoke.",
        bgColor: colorConfig.dangerItem,
    },
    NotInClass: {
        message: "Not Present in Class",
        detail: "",
        bgColor: colorConfig.dangerItem,
    },
    NotInLesson: {
        message: "Not Present in Lesson",
        detail: "The student is not present in the current lesson.",
        bgColor: colorConfig.warningItem,
    },
    Ejected: {
        message: "Ejected from Class",
        detail: "The student has been removed from today's course session and must attend a makeup session on the same day next week. For example, if the ejection occurred on a Tuesday, the student is required to return the following Tuesday to make up for the missed day.",
        bgColor: colorConfig.dangerItem,
    },
    DNCed: {
        message: "DNC - Attention Required",
        detail: "The student is DNC because they have missed 2 or more challenges. You have the option to revoke the DNC.",
        bgColor: colorConfig.dangerItem,
    },
    ClassNotInSession: {
        message: "Class Not in Session",
        detail: "There is no live session currently. Students have access to offline classroom materials to continue their studies.",
        bgColor: colorConfig.warningItem,
    },
};

export { progressMessages, statusMessages, colorConfig };
