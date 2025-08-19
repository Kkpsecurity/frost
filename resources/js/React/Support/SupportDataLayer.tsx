import React, { useEffect, useState } from 'react';
import axios from 'axios';

const SupportDataLayer = () => {
    const [mounted, setMounted] = useState(false);

    useEffect(() => {
        console.log('🔧 SupportDataLayer mounted successfully');
        console.log('📍 Current URL:', window.location.pathname);
        console.log('📍 Current container:', document.getElementById("support-dashboard-container"));
        setMounted(true);
    }, []);

    if (!mounted) {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 text-center p-4">
                        <i className="fas fa-spinner fa-spin fa-2x text-success"></i>
                        <p className="mt-2 text-success"><strong>React Component Initializing...</strong></p>
                        <small className="text-muted">SupportDataLayer is mounting</small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="container-fluid support-center-dashboard">
            <div className="row mb-4">
                <div className="col-12">
                    <div className="alert alert-success">
                        <i className="fas fa-check-circle mr-2"></i>
                        <strong>🚀 React Support Center Dashboard Active!</strong>
                        <br />
                        <small>✅ Component mounted at: {new Date().toLocaleTimeString()}</small>
                        <br />
                        <small>📍 Route: {window.location.pathname}</small>
                    </div>
                </div>
            </div>

            {/* Quick Test Stats */}
            <div className="row mb-4">
                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-info">
                        <div className="inner">
                            <h3>✅</h3>
                            <p>React Loaded</p>
                        </div>
                        <div className="icon">
                            <i className="fab fa-react"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-success">
                        <div className="inner">
                            <h3>🔧</h3>
                            <p>Support Center</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-life-ring"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-warning">
                        <div className="inner">
                            <h3>📊</h3>
                            <p>Dashboard Ready</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>

                <div className="col-lg-3 col-md-6">
                    <div className="small-box bg-primary">
                        <div className="inner">
                            <h3>🎯</h3>
                            <p>Foundation Set</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-bullseye"></i>
                        </div>
                    </div>
                </div>
            </div>

            {/* Test API Connection */}
            <div className="row">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-plug mr-2"></i>
                                Foundation Test
                            </h3>
                        </div>
                        <div className="card-body">
                            <p><strong>✅ React Component:</strong> Successfully mounted and rendering</p>
                            <p><strong>✅ Container:</strong> Found and attached to DOM</p>
                            <p><strong>✅ Route Detection:</strong> {window.location.pathname}</p>
                            <p><strong>✅ Time:</strong> {new Date().toLocaleString()}</p>
                            <hr />
                            <button
                                className="btn btn-primary mr-2"
                                onClick={() => alert('React interaction working!')}
                            >
                                <i className="fas fa-play mr-1"></i>
                                Test Interaction
                            </button>
                            <button
                                className="btn btn-info"
                                onClick={() => console.log('Console test from React component')}
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

export default SupportDataLayer;
