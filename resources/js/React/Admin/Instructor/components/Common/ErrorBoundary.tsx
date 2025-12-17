import React, { ReactNode } from "react";
import { Alert, Container } from "react-bootstrap";

interface ErrorBoundaryProps {
    children: ReactNode;
}

interface ErrorBoundaryState {
    hasError: boolean;
    error: Error | null;
    errorInfo: React.ErrorInfo | null;
}

/**
 * InstructorErrorBoundary - Catches React component errors
 *
 * Wraps the entire Instructor Dashboard app
 * Catches any errors in child components and displays error UI
 * Prevents white screen of death
 */
class InstructorErrorBoundary extends React.Component<
    ErrorBoundaryProps,
    ErrorBoundaryState
> {
    constructor(props: ErrorBoundaryProps) {
        super(props);
        this.state = {
            hasError: false,
            error: null,
            errorInfo: null,
        };
    }

    static getDerivedStateFromError(error: Error): Partial<ErrorBoundaryState> {
        return { hasError: true };
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        console.error(
            "❌ Error caught by InstructorErrorBoundary:",
            error,
            errorInfo
        );
        this.setState({
            error,
            errorInfo,
        });
    }

    render() {
        if (this.state.hasError) {
            return (
                <Container className="my-5">
                    <Alert variant="danger">
                        <Alert.Heading>
                            ⚠️ Something went wrong
                        </Alert.Heading>
                        <p>
                            An error occurred in the Instructor Dashboard. Please
                            try refreshing the page.
                        </p>
                        {process.env.NODE_ENV === "development" && (
                            <div className="mt-3 bg-light p-3 rounded">
                                <p className="mb-2">
                                    <strong>Error Details:</strong>
                                </p>
                                <pre className="mb-0">
                                    {this.state.error?.toString()}
                                </pre>
                                {this.state.errorInfo && (
                                    <pre className="mt-3 mb-0">
                                        {this.state.errorInfo.componentStack}
                                    </pre>
                                )}
                            </div>
                        )}
                    </Alert>
                </Container>
            );
        }

        return this.props.children;
    }
}

export default InstructorErrorBoundary;
