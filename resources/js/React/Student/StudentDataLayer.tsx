import React, { useEffect, useState } from 'react';

const StudentDataLayer = () => {
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        console.log('🎓 StudentDataLayer mounted successfully');
        console.log('📍 Current URL:', window.location.pathname);
        console.log('📍 Current container:', document.getElementById("student-dashboard-container"));
        setMounted(true);
    }, []);

    if (!mounted) {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 text-center p-4">
                        <i className="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p className="mt-2 text-primary"><strong>Student Portal Initializing...</strong></p>
                        <small className="text-muted">StudentDataLayer is mounting</small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="container-fluid student-dashboard">
            <div className="row mb-4">
                <div className="col-12">
                    <div className="alert alert-success">
                        <i className="fas fa-graduation-cap mr-2"></i>
                        <strong>🎓 Student Portal Dashboard Active!</strong>
                        <br />
                        <small>✅ Component mounted at: {new Date().toLocaleTimeString()}</small>
                        <br />
                        <small>📍 Route: {window.location.pathname}</small>
                    </div>
                </div>
            </div>

            {/* Student Dashboard Stats */}
            <div className="row mb-4">
                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-primary">
                        <div className="inner">
                            <h3>📚</h3>
                            <p>My Courses</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-book-open"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-success">
                        <div className="inner">
                            <h3>📈</h3>
                            <p>Progress</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-info">
                        <div className="inner">
                            <h3>📅</h3>
                            <p>Schedule</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-warning">
                        <div className="inner">
                            <h3>🎯</h3>
                            <p>Assignments</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>

            {/* Quick Access */}
            <div className="row mb-4">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-play mr-2"></i>
                                Current Lessons
                            </h3>
                        </div>
                        <div className="card-body">
                            <p><strong>No active lessons at the moment</strong></p>
                            <p className="text-muted">Your upcoming lessons will appear here</p>
                            <button className="btn btn-primary">
                                <i className="fas fa-search mr-1"></i>
                                Browse Courses
                            </button>
                        </div>
                    </div>
                </div>

                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-trophy mr-2"></i>
                                Recent Activity
                            </h3>
                        </div>
                        <div className="card-body">
                            <p><strong>Welcome to your student portal!</strong></p>
                            <p className="text-muted">Your learning activity will be tracked here</p>
                            <button className="btn btn-success">
                                <i className="fas fa-play mr-1"></i>
                                Start Learning
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Foundation Test */}
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-cogs mr-2"></i>
                                Student Portal Foundation
                            </h3>
                        </div>
                        <div className="card-body">
                            <p><strong>✅ React Component:</strong> Successfully mounted and rendering</p>
                            <p><strong>✅ Container:</strong> Found and attached to DOM</p>
                            <p><strong>✅ Route Detection:</strong> {window.location.pathname}</p>
                            <p><strong>✅ Student Portal:</strong> Frontend foundation ready</p>
                            <hr />
                            <button
                                className="btn btn-primary mr-2"
                                onClick={() => alert('Student portal interaction working!')}
                            >
                                <i className="fas fa-play mr-1"></i>
                                Test Interaction
                            </button>
                            <button
                                className="btn btn-info"
                                onClick={() => console.log('Student portal console test')}
                            >
                                <i className="fas fa-terminal mr-1"></i>
                                Test Console
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default StudentDataLayer;
