import React from 'react';

interface OnlineProps {
    instructorData?: any;
    classroomData?: any;
    chatData?: any;
}

const Online: React.FC<OnlineProps> = () => {
    return (
        <div className="p-5">
            <h1>Online</h1>
        </div>
    );
};

export default Online;
