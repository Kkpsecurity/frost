import React from 'react';
import Loader from './Loader';

interface PageLoaderProps {
    show: boolean;
    message?: string;
    blur?: boolean;
    zIndexClass?: string;
}

const PageLoader: React.FC<PageLoaderProps> = ({
    show,
    message = 'Loading…',
    blur = true,
    zIndexClass = 'z-50'
}) => {
    if (!show) {
        return null;
    }

    return (
        <div
            className={`position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-40 ${zIndexClass}`}
            style={{ inset: 0 }}
            aria-busy="true"
            aria-live="polite"
        >
            {blur && (
                <div
                    className="position-absolute top-0 start-0 w-100 h-100"
                    style={{
                        backdropFilter: 'blur(4px)',
                        zIndex: -1
                    }}
                />
            )}
            <div className="text-center">
                <Loader size={48} label={message} />
            </div>
        </div>
    );
};

export default PageLoader;
