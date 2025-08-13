import React, { ReactNode } from "react";

/** ---- Error Boundary ---- */
type EBProps = {
    children: ReactNode;
    onError?: (error: unknown, info?: unknown) => void;
};

type EBState = { hasError: boolean; error?: unknown };


class EnrtyErrorBoundary extends React.Component<EBProps, EBState> {
    state: EBState = { hasError: false };

    static getDerivedStateFromError(error: unknown): EBState {
        return { hasError: true, error };
    }

    componentDidCatch(error: unknown, info: unknown) {
        this.props.onError?.(error, info);
    }

    private handleRetry = () => {
        this.setState({ hasError: false, error: undefined });
        // Hard reset (optional): location.reload();
    };

    render() {
        if (this.state.hasError) {
            return (
                <div style={{ padding: 16 }}>
                    <h3>Something went wrong.</h3>
                    <pre style={{ whiteSpace: "pre-wrap" }}>
                        {String(this.state.error ?? "Unknown error")}
                    </pre>
                    <button onClick={this.handleRetry}>Retry</button>
                </div>
            );
        }
        return this.props.children;
    }
}

export default EnrtyErrorBoundary;
