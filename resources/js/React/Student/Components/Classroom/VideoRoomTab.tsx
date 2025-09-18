import React, { useState } from 'react';
import { Container, Row, Col, Card, Button, Badge, Form, InputGroup } from 'react-bootstrap';

interface Video {
    id: number;
    title: string;
    duration: string;
    thumbnail: string;
    completed: boolean;
    watchedPercent: number;
    description: string;
}

const VideoRoomTab: React.FC = () => {
    const [searchTerm, setSearchTerm] = useState('');
    
    // Mock video data
    const videos: Video[] = [
        {
            id: 1,
            title: "Introduction to Security Principles",
            duration: "15:30",
            thumbnail: "https://via.placeholder.com/300x200/28a745/ffffff?text=Video+1",
            completed: true,
            watchedPercent: 100,
            description: "Overview of basic security principles and concepts"
        },
        {
            id: 2,
            title: "Legal Authority and Limitations",
            duration: "22:45",
            thumbnail: "https://via.placeholder.com/300x200/dc3545/ffffff?text=Video+2",
            completed: false,
            watchedPercent: 65,
            description: "Understanding the legal scope of security officer authority"
        },
        {
            id: 3,
            title: "Emergency Response Procedures",
            duration: "18:20",
            thumbnail: "https://via.placeholder.com/300x200/6c757d/ffffff?text=Video+3",
            completed: false,
            watchedPercent: 0,
            description: "Proper procedures for handling emergency situations"
        },
        {
            id: 4,
            title: "Report Writing Best Practices",
            duration: "12:15",
            thumbnail: "https://via.placeholder.com/300x200/6c757d/ffffff?text=Video+4",
            completed: false,
            watchedPercent: 0,
            description: "How to write clear and effective incident reports"
        },
        {
            id: 5,
            title: "Communication and De-escalation",
            duration: "20:10",
            thumbnail: "https://via.placeholder.com/300x200/6c757d/ffffff?text=Video+5",
            completed: false,
            watchedPercent: 0,
            description: "Effective communication techniques and conflict resolution"
        },
        {
            id: 6,
            title: "Observation and Patrol Techniques",
            duration: "25:40",
            thumbnail: "https://via.placeholder.com/300x200/6c757d/ffffff?text=Video+6",
            completed: false,
            watchedPercent: 0,
            description: "Best practices for security patrols and observation"
        }
    ];

    const filteredVideos = videos.filter(video =>
        video.title.toLowerCase().includes(searchTerm.toLowerCase())
    );

    const getStatusBadge = (video: Video) => {
        if (video.completed) {
            return <Badge bg="success">Completed</Badge>;
        } else if (video.watchedPercent > 0) {
            return <Badge bg="warning">In Progress</Badge>;
        } else {
            return <Badge bg="secondary">Not Started</Badge>;
        }
    };

    const handleVideoClick = (video: Video) => {
        console.log(`Playing video: ${video.title}`);
        // In real implementation, this would open video player
    };

    return (
        <Container fluid className="p-4">
            {/* Header Section */}
            <Row className="mb-4">
                <Col md={8}>
                    <h3 className="text-white fw-bold mb-2">
                        <i className="fas fa-video me-2 text-primary"></i>
                        Course Videos
                    </h3>
                    <p className="text-white-50 mb-0">
                        Watch video lessons to complete your course requirements
                    </p>
                </Col>
                <Col md={4}>
                    <InputGroup>
                        <InputGroup.Text 
                            style={{ 
                                backgroundColor: '#3a4454', 
                                borderColor: '#3a4454',
                                color: '#94a3b8'
                            }}
                        >
                            <i className="fas fa-search"></i>
                        </InputGroup.Text>
                        <Form.Control
                            type="text"
                            placeholder="Search videos..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            style={{
                                backgroundColor: '#2c3448',
                                borderColor: '#3a4454',
                                color: 'white'
                            }}
                        />
                    </InputGroup>
                </Col>
            </Row>

            {/* Progress Summary */}
            <Row className="mb-4">
                <Col>
                    <Card 
                        className="shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Body className="p-3">
                            <Row className="text-center">
                                <Col md={3}>
                                    <div className="stat-item">
                                        <h4 className="text-success mb-0">
                                            {videos.filter(v => v.completed).length}
                                        </h4>
                                        <small className="text-white-50">Completed</small>
                                    </div>
                                </Col>
                                <Col md={3}>
                                    <div className="stat-item">
                                        <h4 className="text-warning mb-0">
                                            {videos.filter(v => v.watchedPercent > 0 && !v.completed).length}
                                        </h4>
                                        <small className="text-white-50">In Progress</small>
                                    </div>
                                </Col>
                                <Col md={3}>
                                    <div className="stat-item">
                                        <h4 className="text-secondary mb-0">
                                            {videos.filter(v => v.watchedPercent === 0).length}
                                        </h4>
                                        <small className="text-white-50">Not Started</small>
                                    </div>
                                </Col>
                                <Col md={3}>
                                    <div className="stat-item">
                                        <h4 className="text-primary mb-0">{videos.length}</h4>
                                        <small className="text-white-50">Total Videos</small>
                                    </div>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            {/* Video Grid */}
            <Row className="g-4">
                {filteredVideos.map((video) => (
                    <Col key={video.id} lg={4} md={6} sm={12}>
                        <Card 
                            className="video-card h-100 shadow-sm"
                            style={{
                                backgroundColor: '#2c3448',
                                border: '1px solid #3a4454',
                                borderRadius: '12px',
                                transition: 'all 0.2s ease',
                                cursor: 'pointer'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.transform = 'translateY(-2px)';
                                e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.transform = 'translateY(0)';
                                e.currentTarget.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                            }}
                            onClick={() => handleVideoClick(video)}
                        >
                            {/* Video Thumbnail */}
                            <div className="position-relative">
                                <Card.Img
                                    variant="top"
                                    src={video.thumbnail}
                                    style={{
                                        height: '180px',
                                        objectFit: 'cover',
                                        borderRadius: '12px 12px 0 0'
                                    }}
                                />
                                
                                {/* Play Button Overlay */}
                                <div 
                                    className="position-absolute top-50 start-50 translate-middle"
                                    style={{
                                        backgroundColor: 'rgba(0,0,0,0.7)',
                                        borderRadius: '50%',
                                        width: '60px',
                                        height: '60px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center'
                                    }}
                                >
                                    <i className="fas fa-play text-white fs-4"></i>
                                </div>

                                {/* Duration Badge */}
                                <Badge 
                                    bg="dark" 
                                    className="position-absolute bottom-0 end-0 m-2"
                                    style={{ opacity: 0.9 }}
                                >
                                    {video.duration}
                                </Badge>

                                {/* Progress Bar */}
                                {video.watchedPercent > 0 && (
                                    <div 
                                        className="position-absolute bottom-0 start-0 w-100"
                                        style={{ height: '4px' }}
                                    >
                                        <div 
                                            style={{
                                                height: '100%',
                                                width: `${video.watchedPercent}%`,
                                                backgroundColor: video.completed ? '#28a745' : '#ffc107',
                                                borderRadius: '0 0 0 12px'
                                            }}
                                        />
                                    </div>
                                )}
                            </div>

                            <Card.Body className="p-3">
                                <div className="d-flex justify-content-between align-items-start mb-2">
                                    <Card.Title 
                                        className="text-white mb-0" 
                                        style={{ 
                                            fontSize: '1rem',
                                            lineHeight: '1.3'
                                        }}
                                    >
                                        {video.title}
                                    </Card.Title>
                                    {getStatusBadge(video)}
                                </div>
                                
                                <Card.Text 
                                    className="text-white-50 mb-3"
                                    style={{ fontSize: '0.9rem' }}
                                >
                                    {video.description}
                                </Card.Text>

                                <Button 
                                    variant={video.completed ? 'success' : video.watchedPercent > 0 ? 'warning' : 'primary'}
                                    size="sm" 
                                    className="w-100 fw-bold"
                                    style={{ borderRadius: '8px' }}
                                >
                                    <i className={`fas fa-${video.completed ? 'check' : 'play'} me-2`}></i>
                                    {video.completed ? 'Completed' : video.watchedPercent > 0 ? 'Continue' : 'Watch'}
                                </Button>
                            </Card.Body>
                        </Card>
                    </Col>
                ))}
            </Row>

            {filteredVideos.length === 0 && (
                <Row className="mt-5">
                    <Col className="text-center">
                        <div className="text-white-50">
                            <i className="fas fa-video fa-3x mb-3"></i>
                            <p>No videos found matching your search.</p>
                        </div>
                    </Col>
                </Row>
            )}
        </Container>
    );
};

export default VideoRoomTab;