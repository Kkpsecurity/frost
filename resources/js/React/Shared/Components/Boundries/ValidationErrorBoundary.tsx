import React, { ReactNode } from "react";
import axios from "axios";
import UAParser from "ua-parser-js";

interface ValidationErrorBoundaryProps {
    children: ReactNode;
}

interface ValidationErrorBoundaryState {
    hasError: boolean;
    error: Error | null;
    errorInfo: React.ErrorInfo | null;
}

class ValidationErrorBoundary extends React.Component<
    ValidationErrorBoundaryProps,
    ValidationErrorBoundaryState
> {
    constructor(props: ValidationErrorBoundaryProps) {
        super(props);
        this.state = {
            hasError: false,
            error: null,
            errorInfo: null,
        };

        this.componentDidCatch = this.componentDidCatch.bind(this);
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        // Here, we are checking if the error is a validation error
        // (this is just an example, adjust according to your error messages or types)
        if (error.message.includes("Validation Error")) {
            this.setState({
                hasError: true,
                error: error,
                errorInfo: errorInfo,
            });

            // Get device information
            const parser = new UAParser();
            const deviceInfo = parser.getResult();

            // Log the error to an error reporting service
            axios.post(window.location.origin + "/services/error/log", {
                error: error.toString(),
                errorInfo: errorInfo.componentStack,
                device: deviceInfo.device, // this will provide info like { model: '', type: '', vendor: '' }
                os: deviceInfo.os,
                browser: deviceInfo.browser,
            });
        }
    }

    render() {
        if (this.state.hasError) {
            return <h1 className="alert alert-danger">Validation error occurred.</h1>;
        }

        return this.props.children; // TypeScript now knows about the children prop
    }
}

export default ValidationErrorBoundary;
