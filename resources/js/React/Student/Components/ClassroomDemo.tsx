import React from 'react';
import { StudentClassroomShell } from './Classroom';

const ClassroomDemo: React.FC = () => {
    return (
        <StudentClassroomShell 
            studentName="John Doe"
            courseTitle="FLORIDA CLASS 'G' 28 HOUR"
        />
    );
};

export default ClassroomDemo;