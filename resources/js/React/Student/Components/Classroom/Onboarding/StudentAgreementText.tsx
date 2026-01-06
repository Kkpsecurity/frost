import React from "react";

/**
 * StudentAgreementText - Displays the full Student Terms and Conditions
 *
 * This is a scrollable read-only component showing the complete STG terms.
 * Reused from archived component - no changes needed.
 */
const StudentAgreementText: React.FC = () => {
    return (
        <div
            style={{
                backgroundColor: "white",
                border: "1px solid #dee2e6",
                borderRadius: "0.375rem",
                padding: "1.5rem",
                maxHeight: "400px",
                overflowY: "scroll",
                fontSize: "0.875rem",
                lineHeight: "1.6",
            }}
        >
            <h5 style={{ marginBottom: "1rem", fontWeight: "bold" }}>
                Student Terms and Conditions
            </h5>

            <ol>
                <li>
                    <strong>Enrollment and Eligibility:</strong> You must be at least 18 years old to enroll in our courses. By enrolling, you confirm that you meet this requirement and that the information provided during registration is accurate and complete.
                </li>

                <li>
                    <strong>Payment and Refunds:</strong> Payment is due upon enrollment unless otherwise specified. Once a course has commenced, no refunds will be provided, except as required by applicable law or under special circumstances determined at our discretion.
                </li>

                <li>
                    <strong>Course Access and Duration:</strong> Upon enrollment, you will receive access to the course materials for a period of six (6) months from the start date. Access may be extended at our discretion or upon request under certain conditions.
                </li>

                <li>
                    <strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your account credentials. Any activity under your account will be considered your responsibility. Notify us immediately if you suspect unauthorized access to your account.
                </li>

                <li>
                    <strong>Attendance and Participation:</strong> You are expected to attend scheduled classes and complete required activities. Repeated absences or failure to participate may result in removal from the course without refund.
                </li>

                <li>
                    <strong>Code of Conduct:</strong> You agree to conduct yourself professionally and respectfully at all times. Harassment, discrimination, or disruptive behavior will not be tolerated and may result in immediate removal from the course.
                </li>

                <li>
                    <strong>Intellectual Property:</strong> All course materials, including videos, documents, and other resources, are the intellectual property of STG (Skills Training Group) or its licensors. You may not reproduce, distribute, or create derivative works from these materials without prior written consent.
                </li>

                <li>
                    <strong>Recording and Screen Capture:</strong> You may not record, screenshot, or otherwise capture any portion of live or recorded class sessions without explicit written permission. Unauthorized recordings violate intellectual property rights and may result in immediate termination and legal action.
                </li>

                <li>
                    <strong>Prohibited Conduct:</strong> The following activities are strictly prohibited:
                    <ul>
                        <li>Sharing your account credentials with others</li>
                        <li>Accessing course materials on behalf of another person</li>
                        <li>Recording or distributing course content</li>
                        <li>Cheating or plagiarism on assignments or exams</li>
                        <li>Using automated tools or bots to complete coursework</li>
                        <li>Disrupting class sessions or harassing instructors/students</li>
                        <li>Attempting to access restricted areas of the platform</li>
                        <li>Posting inappropriate or offensive content</li>
                        <li>Violating any applicable local, state, or federal laws</li>
                    </ul>
                </li>

                <li>
                    <strong>Identity Verification:</strong> As part of enrollment, you may be required to verify your identity through government-issued ID and photo verification. This ensures the integrity of our certification process and prevents fraudulent activity.
                </li>

                <li>
                    <strong>Lesson Challenges:</strong> During live sessions, you may be presented with random verification challenges to confirm your presence and attention. Failure to respond to these challenges may result in the lesson being marked incomplete.
                </li>

                <li>
                    <strong>Certifications and Records:</strong> Upon successful completion of all course requirements, you will receive a certificate of completion. Certificates are only awarded to students who have met all participation, assessment, and conduct requirements.
                </li>

                <li>
                    <strong>Privacy and Data Usage:</strong> We collect and process your personal information in accordance with our Privacy Policy. By enrolling, you consent to the collection and use of your information as described in that policy.
                </li>

                <li>
                    <strong>Technical Requirements:</strong> You are responsible for ensuring that you have the necessary equipment, internet connection, and software to access the course. We are not responsible for technical issues on your end that prevent course access.
                </li>

                <li>
                    <strong>Changes to Terms:</strong> We reserve the right to modify these terms at any time. Changes will be communicated via email or through the platform. Continued use of the course after changes are posted constitutes acceptance of the new terms.
                </li>

                <li>
                    <strong>Limitation of Liability:</strong> To the fullest extent permitted by law, STG shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the course or platform.
                </li>

                <li>
                    <strong>Termination:</strong> We reserve the right to terminate your access to the course at any time for violation of these terms, without refund. You may also request to withdraw from the course, subject to our refund policy.
                </li>

                <li>
                    <strong>Governing Law:</strong> These terms shall be governed by and construed in accordance with the laws of the jurisdiction in which STG operates, without regard to conflict of law principles.
                </li>
            </ol>

            <p style={{ marginTop: "1.5rem", fontStyle: "italic" }}>
                By checking the agreement box and proceeding, you acknowledge that you have read, understood, and agree to be bound by these Student Terms and Conditions.
            </p>
        </div>
    );
};

export default StudentAgreementText;
