import React from 'react';
import { Container, Row, Col, Button, Tabs, Tab } from 'react-bootstrap';
import DashboardTab from './DashboardTab';
import VideoRoomTab from './VideoRoomTab';
import DocumentsTab from './DocumentsTab';

interface ClassroomContentProps {
    courseTitle: string;
    studentName: string;
    activeTab: 'home' | 'videos' | 'documents';
    onTabChange: (tab: 'home' | 'videos' | 'documents') => void;
    onSidebarToggle: () => void;
    sidebarCollapsed: boolean;
}

const ClassroomContent: React.FC<ClassroomContentProps> = ({
    courseTitle,
    studentName,
    activeTab,
    onTabChange,
    onSidebarToggle,
    sidebarCollapsed
}) => {
    return (
        <div className="classroom-content h-100 d-flex flex-column">
            {/* Title Bar */}
            <div 
                className="title-bar p-3 border-bottom"
                style={{
                    backgroundColor: '#1e2442', // Slightly different from main background
                    borderBottomColor: '#3a4454'
                }}
            >
                <Container fluid>
                    <Row className="align-items-center">
                        <Col xs={12} md={8}>
                            <div className="d-flex align-items-center">
                                {/* Mobile sidebar toggle */}
                                <Button
                                    variant="outline-light"
                                    size="sm"
                                    className="d-lg-none me-3"
                                    onClick={onSidebarToggle}
                                >
                                    <i className="fas fa-bars"></i>
                                </Button>

                                {/* Course Icon */}
                                <div 
                                    className="course-icon me-3 d-flex align-items-center justify-content-center"
                                    style={{
                                        width: '40px',
                                        height: '40px',
                                        backgroundColor: '#6f42c1', // Purple icon like current design
                                        borderRadius: '8px',
                                        color: 'white',
                                        fontSize: '1.2rem'
                                    }}
                                >
                                    <i className="fas fa-graduation-cap"></i>
                                </div>

                                {/* Course Title */}
                                <div>
                                    <h4 className="text-white mb-0 fw-bold">
                                        {courseTitle}
                                    </h4>
                                    <small className="text-white-50">
                                        Welcome back, {studentName}
                                    </small>
                                </div>
                            </div>
                        </Col>
                        
                        <Col xs={12} md={4} className="text-end mt-2 mt-md-0">
                            <Button 
                                variant="success" 
                                className="fw-bold px-4"
                                style={{
                                    borderRadius: '8px',
                                    boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.transform = 'translateY(-1px)';
                                    e.currentTarget.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.transform = 'translateY(0)';
                                    e.currentTarget.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                                }}
                            >
                                <i className="fas fa-trophy me-2"></i>
                                Take Exam
                            </Button>
                        </Col>
                    </Row>
                </Container>
            </div>

            {/* Navigation Tabs */}
            <div 
                className="tabs-container"
                style={{
                    backgroundColor: '#1a1f36',
                    borderBottom: '1px solid #3a4454'
                }}
            >
                <Container fluid>
                    <Tabs
                        activeKey={activeTab}
                        onSelect={(k) => onTabChange(k as 'home' | 'videos' | 'documents')}
                        className="custom-tabs"
                        style={{
                            borderBottom: 'none'
                        }}
                    >
                        <Tab 
                            eventKey="home" 
                            title={
                                <span className="px-3 py-2">
                                    <i className="fas fa-home me-2"></i>
                                    Home
                                </span>
                            }
                        />
                        <Tab 
                            eventKey="videos" 
                            title={
                                <span className="px-3 py-2">
                                    <i className="fas fa-video me-2"></i>
                                    Videos
                                </span>
                            }
                        />
                        <Tab 
                            eventKey="documents" 
                            title={
                                <span className="px-3 py-2">
                                    <i className="fas fa-file-alt me-2"></i>
                                    Documents
                                </span>
                            }
                        />
                    </Tabs>
                </Container>
            </div>

            {/* Tab Content */}
            <div className="flex-grow-1" style={{ overflowY: 'auto' }}>
                {activeTab === 'home' && <DashboardTab />}
                {activeTab === 'videos' && <VideoRoomTab />}
                {activeTab === 'documents' && <DocumentsTab />}
            </div>

            {/* Custom CSS for tabs */}
            <style>{`
                .custom-tabs .nav-link {
                    color: #94a3b8 !important;
                    background: transparent !important;
                    border: none !important;
                    border-radius: 0 !important;
                    border-bottom: 3px solid transparent !important;
                    transition: all 0.2s ease !important;
                }
                
                .custom-tabs .nav-link:hover {
                    color: #e2e8f0 !important;
                    border-bottom-color: #6366f1 !important;
                }
                
                .custom-tabs .nav-link.active {
                    color: white !important;
                    background: linear-gradient(135deg, #8b5cf6, #a855f7) !important;
                    border-bottom-color: #a855f7 !important;
                    border-radius: 6px 6px 0 0 !important;
                }
                
                .custom-tabs {
                    border-bottom: 1px solid #3a4454 !important;
                }
            `}</style>
        </div>
    );
};

export default ClassroomContent;