import React, { useState } from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import StudentClassroomSidebar from './StudentClassroomSidebar';
import ClassroomContent from './ClassroomContent';

interface StudentClassroomShellProps {
    studentName?: string;
    courseTitle?: string;
}

const StudentClassroomShell: React.FC<StudentClassroomShellProps> = ({
    studentName = "Student",
    courseTitle = "FLORIDA CLASS 'G' 28 HOUR"
}) => {
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [activeTab, setActiveTab] = useState<'home' | 'videos' | 'documents'>('home');

    const toggleSidebar = () => {
        setSidebarCollapsed(!sidebarCollapsed);
    };

    return (
        <div className="classroom-shell" style={{ 
            minHeight: '100vh',
            backgroundColor: '#1a1f36', // Dark navy background matching current design
            color: 'white',
            overflow: 'hidden'
        }}>
            <Container fluid className="h-100 p-0">
                <Row className="h-100 g-0">
                    {/* Sidebar */}
                    <Col 
                        xs={12} 
                        lg={sidebarCollapsed ? 1 : 3} 
                        className="d-none d-lg-block"
                        style={{
                            transition: 'all 0.3s ease',
                            maxWidth: sidebarCollapsed ? '80px' : '300px',
                            minWidth: sidebarCollapsed ? '80px' : '300px'
                        }}
                    >
                        <StudentClassroomSidebar 
                            collapsed={sidebarCollapsed}
                            onToggle={toggleSidebar}
                        />
                    </Col>

                    {/* Main Content */}
                    <Col xs={12} lg={sidebarCollapsed ? 11 : 9}>
                        <ClassroomContent 
                            courseTitle={courseTitle}
                            studentName={studentName}
                            activeTab={activeTab}
                            onTabChange={setActiveTab}
                            onSidebarToggle={toggleSidebar}
                            sidebarCollapsed={sidebarCollapsed}
                        />
                    </Col>
                </Row>
            </Container>

            {/* Mobile Sidebar Offcanvas - TODO: Implement for mobile */}
        </div>
    );
};

export default StudentClassroomShell;