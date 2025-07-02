import React, { useEffect, useState } from 'react';
import { getPreviousCourses } from '../../../../Hooks/Admin/useInstructorHooks';
import { InstructorType, ValidatedInstructorShape } from '../../../../Config/types';

interface CourseType {
  id: string;
  name: string;
  day: string;
  date: string;
  time: string;
  location: string;
}

type NullableCourse = CourseType | null;

interface InstructorPreviousCoursesProps {
  instructor: InstructorType;
}

const InstructorPreviousCourses: React.FC<InstructorPreviousCoursesProps> = ({
  instructor,
}) => {
  const [previousCourses, setPreviousCourses] = useState<CourseType[]>([]);

  useEffect(() => {
    // Fetch previous courses for the instructor
    const fetchPreviousCourses = async (): Promise<void> => {
      try {
        const courses = await getPreviousCourses(instructor.id);
        setPreviousCourses(courses);
      } catch (error) {
        console.error('Failed to fetch previous courses:', error);
      }
    };

    fetchPreviousCourses();
  }, [instructor]);

  const renderCourseRow = (course: CourseType): JSX.Element => {
    return (
      <tr key={course.id}>
        <td>{course.name}</td>
        <td>{course.day}</td>
        <td>{course.date}</td>
        <td>{course.time}</td>
        <td>{course.location}</td>
        <td>
          <button onClick={() => viewCourseDetails(course.id)}>View Details</button>
        </td>
      </tr>
    );
  };

  const viewCourseDetails = (courseId: string): void => {
    // Logic to navigate to course details page
  };

  return (
    <div>
      <h2>Instructor Previous Courses</h2>
      {previousCourses.length > 0 ? (
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Day</th>
              <th>Date</th>
              <th>Time</th>
              <th>Location</th>
              <th></th>
            </tr>
          </thead>
          <tbody>{previousCourses.map((course) => renderCourseRow(course))}</tbody>
        </table>
      ) : (
        <p>No previous courses found.</p>
      )}
    </div>
  );
};

export default InstructorPreviousCourses;
