import React from 'react';

interface OfflineProps {
    instructorData?: any;
}

const Offline: React.FC<OfflineProps> = () => {
    return (
        <div className="p-5">
            <h1>Offline</h1>
        </div>
    );
};

export default Offline;
