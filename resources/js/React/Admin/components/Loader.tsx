import React from 'react';

interface LoaderProps {
    size?: number;
    label?: string;
    className?: string;
}

const Loader: React.FC<LoaderProps> = ({
    size = 28,
    label = '',
    className = ''
}) => {
    return (
        <div className={`d-flex align-items-center gap-2 ${className}`} role="status">
            <div
                className="spinner-border text-primary flex-shrink-0"
                style={{ width: `${size}px`, height: `${size}px` }}
                role="presentation"
            >
                <span className="visually-hidden">Loading…</span>
            </div>
            {label && <span className="text-muted">{label}</span>}
        </div>
    );
};

export default Loader;
