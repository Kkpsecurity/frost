Root FrostVideoChat Directory:

1. /blocks Directory:

This directory seems to host major building blocks of the application.

    /instructor:
        QueuedUsersBlock.tsx: Likely displays queued users awaiting instructor interaction.
        StudentUsersBlock.tsx: Displays a block of student users, likely for instructor management.
        VideoCallBlock.tsx: A block related to video calling, tailored for the instructor's interface.

    /student:
        StudentVideoCallBlock.tsx: Specifically tailored for students' video calling interface.

    WebRTMAgoraPeer.tsx: A component possibly related to the underlying video chat technology, WebRTC. It's in the root of the blocks directory, suggesting it's fundamental and shared.

2. /Partials Directory:

This directory seems to host partial components that may be reused across the application.

    /instructor:
        ActiveStudentCallList.tsx: Displays a list of active student calls, primarily for the instructor to view and manage.
        MiniVideoCall.tsx: A minimized video call interface, possibly to allow the instructor to manage calls without a full-screen interface.
        StudentListBlock.tsx: Displays student details, tailored for the instructor's viewpoint.
        VideoCall.tsx: Interface component related to video calling.
        VideoCallTabs.tsx: Manages multiple video calls or tabs for calls.
        VideoChatTitleBar.tsx: Title bar for the video chat, likely allowing the instructor to label or identify sessions.

    /student: Currently, this directory is empty, suggesting there may be student-specific partials added in the future.

    /player:
        These components seem focused on video playback and controls, making them shared utilities:
            FrostAgoraPlayer.tsx
            MultiChat.tsx
            PhoneIcons.tsx
            VideoControls.tsx
            VideoPlayer.tsx

    Shared Partials:
        AgoraFrostPlayer.tsx
        DisplayVideoChat.tsx
        Notification.tsx
        Options.tsx: Generic options or settings component.
        Reciever.tsx: Receiving component, possibly for calls or messages.

3. Root Components:

    FrostInstructorVideoChat.tsx: Primary video chat interface for instructors.
    FrostStudentVideoChat.tsx: Primary video chat interface for students.

4. Config or Settings:

    settings.ts: Likely contains configurations or settings for the application.
    share_settings.ts: Possibly settings related to sharing functionalities within the app.