# F.R.O.S.T Data Flow Architecture

Based on the F.R.O.S.T system structure, here's how our React data layer should be organized:

## 🏗️ **System Data Structure**

### **1. Course Data (Template/Structure)**
```
Course
├── Lessons (individual lesson content)
├── Course Units (Day 1, Day 2, etc.)
└── Course Unit Lessons (4 lessons for Day 2)
```
**Purpose**: Static course templates and structure
**Polling**: Low frequency (15+ minutes) - content rarely changes

### **2. Student Data (Progress Tracking)**
```
Student/User
├── Student Units (course units taken)
├── Student Lessons (lessons completed per day)
└── Challenges (lesson validation/engagement)
```
**Purpose**: Track student progress and engagement
**Polling**: Medium frequency (5-10 minutes) - progress updates

### **3. Instructor Data (Teaching Progress)**
```
InstUnit (copy of course unit for teaching day)
└── InstLessons (copy of lessons for specific day)
```
**Purpose**: Track instructor teaching progress and current session
**Polling**: Medium frequency (5 minutes) - teaching progress

### **4. Classroom Data (Session Management)**
```
CourseDate (scheduled sessions)
├── Session Status (Scheduled/Live/Done)
├── Instructor Assignment
└── Student Roster
```
**Purpose**: Real-time classroom session management
**Polling**: High frequency (2 minutes) - real-time status

## 🎯 **React Data Layer Design**

Based on this structure, our data layer should be:

```tsx
<InstructorDataLayer instructorId={id}>
  {/* Loads all data with appropriate polling intervals */}
  
  <InstructorContext value={{
    // Course Data (15-min cache) - from SettingsContext
    courses,
    lessons,
    courseUnits,
    
    // Instructor Data (5-min polling) - from useInstructorProgress
    instUnits,        // Teaching units
    instLessons,      // Teaching lessons
    progressStats,
    
    // Classroom Data (2-min polling) - from useClassroomSchedule
    courseDates,      // Scheduled sessions
    activeSessions,   // Live classroom sessions
    weeklySchedule,
    
    // Student Data (if needed for instructor view)
    studentUnits,     // Students in current sessions
    studentProgress,  // For monitoring engagement
  }}>
    {children}
  </InstructorContext>
</InstructorDataLayer>
```

## 📊 **Data Relationships**

```
Course (template)
    ↓ creates
CourseUnit (Day 1, Day 2)
    ↓ scheduled as
CourseDate (Aug 14, 9:00 AM)
    ↓ instructor claims
InstUnit (instructor's copy for teaching)
    ↓ contains
InstLessons (lessons instructor will teach)
    ↓ students join
StudentUnits (student progress for that day)
```

## 🔄 **Polling Strategy by Data Type**

### **Course Data (Static)**
- **Frequency**: 15+ minutes
- **Reason**: Course structure rarely changes
- **Context**: SettingsContext (existing)

### **Instructor Data (Teaching Progress)**
- **Frequency**: 5 minutes
- **Reason**: Instructor progress updates moderately
- **Hook**: useInstructorProgress (InstUnit, InstLessons)

### **Classroom Data (Real-time Sessions)**
- **Frequency**: 2 minutes  
- **Reason**: Session status changes frequently
- **Hook**: useClassroomSchedule (CourseDate, session status)

### **Student Data (Progress Monitoring)**
- **Frequency**: 5-10 minutes
- **Reason**: Student progress updates moderately
- **Hook**: useStudentProgress (if needed for instructor dashboard)

## 🎪 **Instructor Dashboard Flow**

```
1. Instructor visits /admin/instructors
2. InstructorDataLayer loads:
   - Course templates (cached)
   - Instructor's active InstUnits & InstLessons
   - This week's CourseDate sessions
   - Student progress in active sessions

3. Dashboard Panel decides:
   - IF: Instructor has active InstUnit → Show teaching controls
   - IF: Sessions available to claim → Show session cards
   - ELSE: Show bulletin board with next scheduled CourseDate

4. Take Control Action:
   - Claims CourseDate → Creates InstUnit → Generates InstLessons
   - Navigates to classroom with real-time session data
```

## 🚀 **Implementation Benefits**

### **✅ Matches F.R.O.S.T Architecture**
- Mirrors the actual database structure
- Clear separation of concerns
- Proper data relationships

### **✅ Optimized Performance**
- Different polling for different data types
- Efficient cache management
- Real-time updates where needed

### **✅ Scalable Design**
- Easy to add student monitoring features
- Clear data flow for debugging
- Consistent with Laravel models

This structure perfectly aligns with the F.R.O.S.T system and provides the foundation for a robust instructor dashboard!
