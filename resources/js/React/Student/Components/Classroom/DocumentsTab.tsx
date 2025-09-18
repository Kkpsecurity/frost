import React, { useState } from 'react';
import { Container, Row, Col, Card, Button, Badge, Form, InputGroup, ListGroup } from 'react-bootstrap';

interface Document {
    id: number;
    name: string;
    type: 'pdf' | 'doc' | 'txt' | 'ppt';
    size: string;
    uploadDate: string;
    category: 'course-material' | 'assignment' | 'reference' | 'certificate';
    description: string;
    downloadUrl?: string;
}

const DocumentsTab: React.FC = () => {
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCategory, setSelectedCategory] = useState<string>('all');
    
    // Mock documents data
    const documents: Document[] = [
        {
            id: 1,
            name: "Florida Security Officer Handbook.pdf",
            type: 'pdf',
            size: "2.5 MB",
            uploadDate: "2024-03-15",
            category: 'course-material',
            description: "Official handbook covering all security officer regulations and procedures",
            downloadUrl: "#"
        },
        {
            id: 2,
            name: "Legal Authority Guidelines.pdf",
            type: 'pdf',
            size: "1.8 MB",
            uploadDate: "2024-03-15",
            category: 'course-material',
            description: "Detailed guidelines on legal authority and limitations for security officers"
        },
        {
            id: 3,
            name: "Assignment 1 - Case Study Analysis.doc",
            type: 'doc',
            size: "450 KB",
            uploadDate: "2024-03-20",
            category: 'assignment',
            description: "Case study analysis assignment for Module 1"
        },
        {
            id: 4,
            name: "Emergency Procedures Checklist.pdf",
            type: 'pdf',
            size: "850 KB",
            uploadDate: "2024-03-22",
            category: 'reference',
            description: "Quick reference checklist for emergency response procedures"
        },
        {
            id: 5,
            name: "Training Slides - Communication Skills.ppt",
            type: 'ppt',
            size: "3.2 MB",
            uploadDate: "2024-03-25",
            category: 'course-material',
            description: "PowerPoint presentation on effective communication techniques"
        },
        {
            id: 6,
            name: "Course Completion Certificate Template.pdf",
            type: 'pdf',
            size: "1.1 MB",
            uploadDate: "2024-03-30",
            category: 'certificate',
            description: "Template for course completion certificate"
        },
        {
            id: 7,
            name: "Study Guide - Final Exam.txt",
            type: 'txt',
            size: "125 KB",
            uploadDate: "2024-04-01",
            category: 'reference',
            description: "Comprehensive study guide for the final examination"
        }
    ];

    const categories = [
        { value: 'all', label: 'All Documents', icon: 'fas fa-files' },
        { value: 'course-material', label: 'Course Materials', icon: 'fas fa-book' },
        { value: 'assignment', label: 'Assignments', icon: 'fas fa-tasks' },
        { value: 'reference', label: 'Reference', icon: 'fas fa-bookmark' },
        { value: 'certificate', label: 'Certificates', icon: 'fas fa-certificate' }
    ];

    const filteredDocuments = documents.filter(doc => {
        const matchesSearch = doc.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            doc.description.toLowerCase().includes(searchTerm.toLowerCase());
        const matchesCategory = selectedCategory === 'all' || doc.category === selectedCategory;
        return matchesSearch && matchesCategory;
    });

    const getFileIcon = (type: string) => {
        switch (type) {
            case 'pdf': return 'fas fa-file-pdf text-danger';
            case 'doc': return 'fas fa-file-word text-primary';
            case 'txt': return 'fas fa-file-alt text-secondary';
            case 'ppt': return 'fas fa-file-powerpoint text-warning';
            default: return 'fas fa-file text-secondary';
        }
    };

    const getCategoryBadge = (category: string) => {
        const badgeConfig = {
            'course-material': { bg: 'primary', text: 'Course Material' },
            'assignment': { bg: 'warning', text: 'Assignment' },
            'reference': { bg: 'info', text: 'Reference' },
            'certificate': { bg: 'success', text: 'Certificate' }
        };
        
        const config = badgeConfig[category as keyof typeof badgeConfig] || { bg: 'secondary', text: 'Other' };
        return <Badge bg={config.bg}>{config.text}</Badge>;
    };

    const handleDownload = (document: Document) => {
        console.log(`Downloading: ${document.name}`);
        // In real implementation, this would trigger download
    };

    const handleView = (document: Document) => {
        console.log(`Viewing: ${document.name}`);
        // In real implementation, this would open document viewer
    };

    return (
        <Container fluid className="p-4">
            {/* Header Section */}
            <Row className="mb-4">
                <Col md={6}>
                    <h3 className="text-white fw-bold mb-2">
                        <i className="fas fa-file-alt me-2 text-info"></i>
                        Course Documents
                    </h3>
                    <p className="text-white-50 mb-0">
                        Access your course materials, assignments, and reference documents
                    </p>
                </Col>
                <Col md={6}>
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
                            placeholder="Search documents..."
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

            <Row>
                {/* Sidebar - Categories */}
                <Col md={3} className="mb-4">
                    <Card 
                        className="shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Header 
                            className="py-3"
                            style={{
                                backgroundColor: '#3a4454',
                                borderBottom: '1px solid #3a4454',
                                borderRadius: '12px 12px 0 0'
                            }}
                        >
                            <h6 className="text-white mb-0 fw-bold">
                                <i className="fas fa-filter me-2"></i>
                                Categories
                            </h6>
                        </Card.Header>
                        <ListGroup variant="flush">
                            {categories.map((category) => (
                                <ListGroup.Item
                                    key={category.value}
                                    action
                                    active={selectedCategory === category.value}
                                    onClick={() => setSelectedCategory(category.value)}
                                    className="border-0"
                                    style={{
                                        backgroundColor: selectedCategory === category.value ? '#6366f1' : '#2c3448',
                                        color: 'white',
                                        borderColor: '#3a4454'
                                    }}
                                >
                                    <i className={`${category.icon} me-2`}></i>
                                    {category.label}
                                    <Badge 
                                        bg="secondary" 
                                        className="float-end"
                                        style={{ fontSize: '0.7rem' }}
                                    >
                                        {category.value === 'all' 
                                            ? documents.length 
                                            : documents.filter(d => d.category === category.value).length
                                        }
                                    </Badge>
                                </ListGroup.Item>
                            ))}
                        </ListGroup>
                    </Card>
                </Col>

                {/* Main Content - Documents List */}
                <Col md={9}>
                    <Card 
                        className="shadow-sm"
                        style={{
                            backgroundColor: '#2c3448',
                            border: '1px solid #3a4454',
                            borderRadius: '12px'
                        }}
                    >
                        <Card.Header 
                            className="py-3"
                            style={{
                                backgroundColor: '#3a4454',
                                borderBottom: '1px solid #3a4454',
                                borderRadius: '12px 12px 0 0'
                            }}
                        >
                            <div className="d-flex justify-content-between align-items-center">
                                <h6 className="text-white mb-0 fw-bold">
                                    <i className="fas fa-list me-2"></i>
                                    Documents ({filteredDocuments.length})
                                </h6>
                                <Button 
                                    variant="outline-light" 
                                    size="sm"
                                    onClick={() => console.log('Download all')}
                                >
                                    <i className="fas fa-download me-1"></i>
                                    Download All
                                </Button>
                            </div>
                        </Card.Header>

                        {filteredDocuments.length > 0 ? (
                            <ListGroup variant="flush">
                                {filteredDocuments.map((document) => (
                                    <ListGroup.Item
                                        key={document.id}
                                        className="border-0 py-3"
                                        style={{
                                            backgroundColor: '#2c3448',
                                            borderBottom: '1px solid #3a4454',
                                            color: 'white'
                                        }}
                                    >
                                        <Row className="align-items-center">
                                            <Col md={1} className="text-center">
                                                <i className={`${getFileIcon(document.type)} fa-2x`}></i>
                                            </Col>
                                            <Col md={6}>
                                                <h6 className="text-white mb-1 fw-bold">
                                                    {document.name}
                                                </h6>
                                                <p className="text-white-50 mb-2" style={{ fontSize: '0.9rem' }}>
                                                    {document.description}
                                                </p>
                                                <div className="d-flex align-items-center gap-2">
                                                    {getCategoryBadge(document.category)}
                                                    <small className="text-white-50">
                                                        {document.size} • Uploaded {document.uploadDate}
                                                    </small>
                                                </div>
                                            </Col>
                                            <Col md={5} className="text-end">
                                                <div className="btn-group">
                                                    <Button
                                                        variant="outline-primary"
                                                        size="sm"
                                                        onClick={() => handleView(document)}
                                                        className="me-2"
                                                    >
                                                        <i className="fas fa-eye me-1"></i>
                                                        View
                                                    </Button>
                                                    <Button
                                                        variant="outline-success"
                                                        size="sm"
                                                        onClick={() => handleDownload(document)}
                                                    >
                                                        <i className="fas fa-download me-1"></i>
                                                        Download
                                                    </Button>
                                                </div>
                                            </Col>
                                        </Row>
                                    </ListGroup.Item>
                                ))}
                            </ListGroup>
                        ) : (
                            <Card.Body className="text-center py-5">
                                <div className="text-white-50">
                                    <i className="fas fa-file-alt fa-3x mb-3"></i>
                                    <p>No documents found matching your criteria.</p>
                                    <Button variant="outline-light" size="sm" onClick={() => {
                                        setSearchTerm('');
                                        setSelectedCategory('all');
                                    }}>
                                        Clear Filters
                                    </Button>
                                </div>
                            </Card.Body>
                        )}
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default DocumentsTab;