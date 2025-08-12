# Instructor Classroom System

## Overview
The Instructor Classroom System is a React-based interface that provides instructors with a comprehensive dashboard to manage their course dates, students, and classroom activities. The system adapts based on whether the instructor has scheduled courses or not.

## System Requirements

### 1. Instructor Dashboard Entry Point
- **Route**: `/instructor/dashboard` or `/instructor/classroom`
- **Authentication**: Requires instructor role/permissions
- **Initial Check**: System checks for scheduled course dates for the logged-in instructor

### 2. Two Primary States

#### A. Scheduled Courses Available
When instructor has scheduled course dates:
- Display upcoming course schedule
- Show current/active courses
- Display enrolled students for each course
- Quick access to course materials and resources
- Real-time class management tools

#### B. No Scheduled Courses (Bulletin Board)
When no courses are scheduled:
- Display bulletin board interface (teacher break room style)
- Show general announcements and information
- Display available courses for scheduling
- Show instructor resources and training materials
- Provide quick links to administrative functions

## Technical Architecture

### Frontend (React Components)

#### Core Components Structure

FOcus Only on this part

InstructorDashboard
    ClassroomInterface
        BulletinBoard  // no course_dates
        ClassDashboard // Shows list of Coursedate
    

### Backend API Endpoints

#### Course Schedule Endpoints
- `GET /api/instructor/course-dates` - Get instructor's scheduled courses
- `GET /api/instructor/course-dates/{id}/students` - Get students for specific course
- `POST /api/instructor/course-dates/{id}/attendance` - Mark attendance
- `PUT /api/instructor/course-dates/{id}/progress` - Update lesson progress

#### Bulletin Board Endpoints
- `GET /api/instructor/announcements` - Get system announcements
- `GET /api/instructor/available-courses` - Get courses available for scheduling
- `GET /api/instructor/resources` - Get instructor resources and materials

#### Dashboard Data Endpoint
- `GET /api/instructor/dashboard` - Get complete dashboard data including:
  - Scheduled courses count
  - Active courses
  - Bulletin board content
  - Quick stats

## Feature Specifications

### 1. Instructor Dashboard (Main Entry)

#### Initial Data Loading
- Check authentication and instructor permissions
- Query scheduled course dates for instructor
- Load dashboard configuration and preferences
- Determine which interface to display

#### Dashboard States
```typescript
interface DashboardState {
  hasScheduledCourses: boolean;
  activeCourses: CourseDate[];
  upcomingCourses: CourseDate[];
  bulletinContent: BulletinBoardData;
  instructorProfile: InstructorProfile;
}
```

### 2. Scheduled Courses Interface

#### Course Schedule Overview
- **Current/Active Courses**: Courses happening today or currently in progress
- **Upcoming Courses**: Future scheduled courses (next 7-30 days)
- **Recent Courses**: Recently completed courses (last 7 days)

#### Course Card Information
- Course title and type (D Course, G Course)
- Date and time information
- Duration and location
- Number of enrolled students
- Course progress status
- Quick action buttons (Enter Classroom, View Students, Materials)

#### Student Management
- View enrolled students list
- Mark attendance (present/absent/late)
- Track individual student progress
- Add notes about student performance
- Send communications to students

### 3. Active Classroom Interface

#### Real-time Classroom Tools
- **Attendance Tracker**: Mark students present/absent/late
- **Lesson Progress**: Track completion of lessons and units
- **Timer/Clock**: Display current time and elapsed class time
- **Break Timer**: Manage break times during long courses
- **Emergency Contacts**: Quick access to emergency information

#### Lesson Management
- Display current lesson information
- Track lesson completion status
- Add instructor notes for each lesson
- Mark lesson objectives as completed
- Upload or access lesson materials

### 4. Bulletin Board Interface

#### Announcements Section
- System-wide announcements from administration
- Training updates and new resources
- Policy changes and important notices
- Emergency notifications

#### Available Courses
- List of courses available for scheduling
- Course requirements and prerequisites
- Instructor eligibility information
- Quick scheduling request functionality

#### Instructor Resources
- Training materials and documentation
- Best practices and teaching guides
- Technical support resources
- Contact information for support staff

#### Quick Links
- Link to course scheduling system
- Access to instructor training modules
- Profile and preference management
- Help and support documentation

## Data Models

### CourseDate (Extended)
```typescript
interface CourseDate {
  id: number;
  course_id: number;
  course_unit_id: number;
  instructor_id: number;
  starts_at: string;
  ends_at: string;
  is_active: boolean;
  student_count: number;
  course: Course;
  course_unit: CourseUnit;
  students: Student[];
  attendance_records: AttendanceRecord[];
}
```

### AttendanceRecord
```typescript
interface AttendanceRecord {
  id: number;
  course_date_id: number;
  student_id: number;
  status: 'present' | 'absent' | 'late' | 'excused';
  marked_at: string;
  marked_by_instructor_id: number;
  notes?: string;
}
```

### BulletinBoardData
```typescript
interface BulletinBoardData {
  announcements: Announcement[];
  available_courses: Course[];
  instructor_resources: Resource[];
  quick_stats: {
    total_instructors: number;
    active_courses_today: number;
    students_in_system: number;
  };
}
```

## User Experience Flow

### 1. Instructor Login and Navigation
1. Instructor logs into system
2. Navigates to instructor dashboard/classroom
3. System checks for scheduled courses
4. Redirects to appropriate interface

### 2. With Scheduled Courses
1. Display course schedule overview
2. Show active/current courses prominently
3. Allow instructor to "Enter Classroom" for active courses
4. Provide quick access to student lists and materials

### 3. Active Classroom Session
1. Display classroom management interface
2. Show student attendance grid
3. Track lesson progress in real-time
4. Provide tools for breaks, notes, and communication

### 4. No Scheduled Courses
1. Display bulletin board interface
2. Show relevant announcements and information
3. Provide access to scheduling tools
4. Display instructor resources and training

## Implementation Priority

### Phase 1: Core Infrastructure
- [ ] Set up React component structure
- [ ] Create basic API endpoints for instructor data
- [ ] Implement authentication and permission checks
- [ ] Build main dashboard container component

### Phase 2: Scheduled Courses Interface
- [ ] Course schedule overview component
- [ ] Course card display with essential information
- [ ] Student list and basic management
- [ ] Navigation to classroom interface

### Phase 3: Active Classroom Tools
- [ ] Attendance tracking functionality
- [ ] Lesson progress tracking
- [ ] Real-time classroom management tools
- [ ] Notes and communication features

### Phase 4: Bulletin Board System
- [ ] Announcements display system
- [ ] Available courses interface
- [ ] Instructor resources section
- [ ] Quick links and navigation

### Phase 5: Advanced Features
- [ ] Real-time updates and notifications
- [ ] Mobile responsiveness optimization
- [ ] Performance optimization
- [ ] Advanced reporting and analytics

## Technical Considerations

### State Management
- Use React Query for API data management
- Implement proper loading and error states
- Cache frequently accessed data
- Handle real-time updates efficiently

### Responsive Design
- Mobile-first approach for tablet/phone usage
- Touch-friendly interface for classroom use
- Accessible design for all instructors
- Fast loading for classroom environment

### Performance
- Lazy load components not immediately needed
- Optimize API calls and data fetching
- Implement proper caching strategies
- Monitor bundle size and loading times

### Security
- Verify instructor permissions on all endpoints
- Secure student data and attendance records
- Implement proper session management
- Audit trail for classroom activities

## Success Metrics

### Instructor Adoption
- Percentage of instructors using the classroom system
- Frequency of system usage during classes
- User satisfaction scores and feedback

### Operational Efficiency
- Time saved in attendance tracking
- Accuracy of lesson progress reporting
- Reduction in administrative overhead

### Student Experience Impact
- Improved attendance accuracy
- Better communication with instructors
- Enhanced classroom experience feedback

## Future Enhancements

### Integration Opportunities
- LMS integration for course materials
- Zoom integration for virtual classrooms
- Mobile app for instructors
- Student-facing classroom interface

### Advanced Features
- AI-powered insights and recommendations
- Automated attendance using facial recognition
- Advanced analytics and reporting
- Integration with payroll for instructor hours

---

**Status**: Planning Phase
**Owner**: Development Team
**Priority**: High
**Estimated Timeline**: 6-8 weeks
**Dependencies**: Course Management System, User Authentication, API Infrastructure
