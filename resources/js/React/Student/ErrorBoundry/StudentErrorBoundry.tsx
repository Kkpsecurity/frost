import React, { Component, ReactNode } from 'react';

interface Props {
    children: ReactNode;
    onError?: (error: Error, errorInfo: React.ErrorInfo) => void;
}

interface State {
    hasError: boolean;
    error?: Error;
}

class StudentErrorBoundary extends Component<Props, State> {
    constructor(props: Props) {
        super(props);
        this.state = { hasError: false };
    }

    static getDerivedStateFromError(error: Error): State {
        return { hasError: true, error };
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        console.error('Student Error Boundary caught an error:', error, errorInfo);

        if (this.props.onError) {
            this.props.onError(error, errorInfo);
        }
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-12">
                            <div className="alert alert-danger">
                                <h4>
                                    <i className="fas fa-exclamation-triangle mr-2"></i>
                                    Student Dashboard Error
                                </h4>
                                <p>
                                    Something went wrong with the student dashboard.
                                    Please refresh the page or contact support if the problem persists.
                                </p>
                                <details className="mt-3">
                                    <summary>Technical Details</summary>
                                    <pre className="mt-2 text-muted small">
                                        {this.state.error?.message}
                                        {'\n'}
                                        {this.state.error?.stack}
                                    </pre>
                                </details>
                                <button
                                    className="btn btn-outline-danger mt-3"
                                    onClick={() => window.location.reload()}
                                >
                                    <i className="fas fa-refresh mr-1"></i>
                                    Reload Page
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

export default StudentErrorBoundary;
