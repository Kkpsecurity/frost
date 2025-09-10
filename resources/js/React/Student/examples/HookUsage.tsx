/**
 * Example Usage of Student and Class Data Hooks
 * Demonstrates proper implementation with loading, error, and success states
 */

import React from 'react';
import { useGetStudentData, useGetClassData } from '../hooks';
import type { UseGetClassDataOptions } from '../types/classroom';

// Example: Student Profile Component
export const StudentProfile: React.FC = () => {
  const { data: studentData, isLoading, error, refetch } = useGetStudentData();

  if (isLoading) {
    return (
      <div role="status" aria-label="Loading student data">
        Loading student profile...
      </div>
    );
  }

  if (error) {
    return (
      <div role="alert" aria-label="Error loading student data">
        <p>Error loading student data: {error.message}</p>
        <button onClick={() => refetch()}>Try Again</button>
      </div>
    );
  }

  if (!studentData) {
    return <div>No student data available</div>;
  }

  return (
    <div>
      <h2>Student Profile</h2>
      <p>Name: {studentData.student.name || `${studentData.student.fname} ${studentData.student.lname}`}</p>
      <p>Email: {studentData.student.email}</p>
      <p>Active Courses: {studentData.courseAuth.length}</p>
      <p>Status: {studentData.student.is_active ? 'Active' : 'Inactive'}</p>
    </div>
  );
};

// Example: Classroom Component with filtering options
interface ClassroomProps {
  classId: string;
}

export const Classroom: React.FC<ClassroomProps> = ({ classId }) => {
  const [isLive, setIsLive] = React.useState<boolean>(false);
  const [selectedDate, setSelectedDate] = React.useState<string>('');

  const options: UseGetClassDataOptions = {
    ...(selectedDate && { date: selectedDate }),
    ...(isLive !== undefined && { isLive })
  };

  const { data: classData, isLoading, error, refetch } = useGetClassData(classId, options);

  if (isLoading) {
    return (
      <div role="status" aria-label="Loading classroom data">
        Loading classroom information...
      </div>
    );
  }

  if (error) {
    return (
      <div role="alert" aria-label="Error loading classroom data">
        <p>Error loading classroom: {error.message}</p>
        <button onClick={() => refetch()}>Retry</button>
      </div>
    );
  }

  if (!classData) {
    return <div>No classroom data available</div>;
  }

  return (
    <div>
      <h2>Classroom Information</h2>

      {/* Filter Controls */}
      <div>
        <label>
          <input
            type="checkbox"
            checked={isLive}
            onChange={(e) => setIsLive(e.target.checked)}
          />
          Live Sessions Only
        </label>

        <label>
          Date Filter:
          <input
            type="date"
            value={selectedDate}
            onChange={(e) => setSelectedDate(e.target.value)}
          />
        </label>

        <button onClick={() => refetch()}>Refresh</button>
      </div>

      {/* Instructors */}
      <section>
        <h3>Instructors ({classData.instructors.length})</h3>
        {classData.instructors.map((instructor) => (
          <div key={instructor.id}>
            <h4>{instructor.name}</h4>
            <p>Email: {instructor.email}</p>
            <p>Specialties: {instructor.specialties.join(', ')}</p>
            {instructor.rating && <p>Rating: {instructor.rating}/5</p>}
          </div>
        ))}
      </section>

      {/* Course Dates */}
      <section>
        <h3>Course Schedule ({classData.courseDates.length})</h3>
        {classData.courseDates.map((courseDate) => (
          <div key={courseDate.id}>
            <h4>{courseDate.course_title}</h4>
            <p>Dates: {courseDate.start_date} to {courseDate.end_date}</p>
            <p>Time: {courseDate.start_time} - {courseDate.end_time}</p>
            <p>Location: {courseDate.location}</p>
            <p>Status: {courseDate.status}</p>
            <p>Enrollment: {courseDate.current_enrollment}/{courseDate.max_students}</p>
            {courseDate.meeting_link && (
              <a href={courseDate.meeting_link} target="_blank" rel="noopener noreferrer">
                Join Session
              </a>
            )}
          </div>
        ))}
      </section>
    </div>
  );
};

// Example: Combined Dashboard
export const StudentDashboard: React.FC = () => {
  const { data: studentData } = useGetStudentData();
  const firstCourseId = studentData?.courseAuth[0]?.course_id?.toString();

  const { data: classData } = useGetClassData(firstCourseId || '1', {
    isLive: true
  });

  return (
    <div>
      <h1>Student Dashboard</h1>
      <StudentProfile />
      {firstCourseId && <Classroom classId={firstCourseId} />}
    </div>
  );
};
