/**
 * Props Types Index
 * 
 * Centralized export for all component prop types.
 * Provides clean imports and separation between student and classroom contexts.
 */

// Classroom Props
export * from './classroom.props';

// Student Props  
export * from './student.props';

// Type Guards for distinguishing between contexts
export const isClassroomStudent = (user: any): user is import('./classroom.props').ClassroomStudent => {
    return user && typeof user === 'object' && 'class' in user;
};

export const isDashboardStudent = (user: any): user is import('./student.props').DashboardStudent => {
    return user && typeof user === 'object' && 'enrollment_status' in user;
};

export const isClassroomInstructor = (user: any): user is import('./classroom.props').ClassroomInstructor => {
    return user && typeof user === 'object' && user.role_id && user.role_id !== 5;
};
