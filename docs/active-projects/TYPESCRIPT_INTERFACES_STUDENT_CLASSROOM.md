# TypeScript Interfaces for Student Classroom Data

**Task Status:** 🔵 Ready to Start  
**Priority:** High  
**Focus:** Create TypeScript interfaces and types for incoming data from StudentDashboardService and ClassroomDashboardService

---

## 🎯 OBJECTIVE

Create comprehensive TypeScript interfaces and types for the two main data structures returned from the backend services:
1. **Classroom Data** - `instructors` and `courseDates` arrays
2. **Student Data** - `student` object and `courseAuth` array

---

## 📊 DATA STRUCTURE ANALYSIS

Based on the current service implementation and debug endpoints, we have:

### **Backend Data Sources:**
- `StudentDashboardService::getClassData()` → Returns classroom data
- `StudentDashboardService::getStudentData()` → Returns student data  
- `ClassroomDashboardService::getClassroomData()` → Returns classroom data
- Debug endpoints: `/classroom/debug/class` and `/classroom/debug/student`

### **Current Data Flow:**
```
Backend Services → Controllers → JSON API → Frontend TypeScript
```

---

## 🔧 TYPESCRIPT IMPLEMENTATION TASKS

### **Task 1: Core Interface Definitions**

Create base TypeScript interfaces in: `resources/js/types/student-classroom.ts`

#### **1.1 Instructor Interface**
```typescript
interface Instructor {
  id: number;
  name: string;
  email: string;
  phone: string;
  bio: string;
  certifications: string[];
  profile_image: string;
  specialties: string[];
  rating: number;
  total_courses: number;
  years_experience: number;
}
```

#### **1.2 Course Date Interface**
```typescript
interface CourseDate {
  id: number;
  course_id: number;
  instructor_id: number;
  start_date: string; // ISO date string
  end_date: string;   // ISO date string
  start_time: string; // HH:MM:SS format
  end_time: string;   // HH:MM:SS format
  timezone: string;
  location: string;
  status: 'active' | 'scheduled' | 'completed' | 'cancelled';
  max_students: number;
  current_enrollment: number;
  meeting_link?: string; // Optional for online classes
  course_title: string;
  created_at: string; // ISO datetime string
  updated_at: string; // ISO datetime string
}
```

#### **1.3 Student Interface**
```typescript
interface Student {
  id: number;
  fname: string;
  lname: string;
  email: string;
  email_verified_at?: string | null;
  password?: string; // Should this be included?
  remember_token?: string; // Should this be included?
  avatar?: string | null;
  use_gravatar: boolean;
  student_info: StudentInfo | null;
  email_opt_in: boolean;
  created_at: string;
  updated_at: string;
}

interface StudentInfo {
  fname: string;
  middle_initial?: string | null;
  lname: string;
  email: string;
  suffix?: string | null;
  dob?: string | null;
  phone?: string | null;
}
```

#### **1.4 Course Authorization Interface**
```typescript
interface CourseAuth {
  id: number;
  user_id: number;
  course_id: number;
  created_at: string;
  updated_at: string;
  agreed_at?: string | null;
  completed_at?: string | null;
  is_passed: boolean;
  start_date?: string | null;
  expire_date?: string | null;
  disabled_at?: string | null;
  disabled_reason?: string | null;
  submitted_at?: string | null;
  submitted_by?: number | null;
  dol_tracking?: string | null;
  exam_admin_id?: number | null;
  range_date_id?: number | null;
  id_override: boolean;
}
```

### **Task 2: Main Data Structure Interfaces**

#### **2.1 Classroom Data Interface**
```typescript
interface ClassroomData {
  instructors: Instructor[];
  courseDates: CourseDate[];
}
```

#### **2.2 Student Data Interface**  
```typescript
interface StudentData {
  student: Student;
  courseAuth: CourseAuth[];
}
```

#### **2.3 Combined Dashboard Data Interface**
```typescript
interface DashboardData {
  classroomData: ClassroomData;
  studentData: StudentData;
  debug_info?: {
    data_source: 'database' | 'sample_data';
    student_service_structure: string[];
    classroom_service_structure: string[];
    has_course_data: boolean;
    has_instructor_data: boolean;
    has_course_dates: boolean;
  };
}
```

### **Task 3: API Response Types**

#### **3.1 API Response Wrappers**
```typescript
// For debug endpoints
type ApiResponse<T> = {
  data: T;
  message?: string;
  error?: string;
};

// For classroom debug endpoint
type ClassroomApiResponse = ApiResponse<ClassroomData>;

// For student debug endpoint  
type StudentApiResponse = ApiResponse<StudentData>;

// For combined debug endpoint
type DashboardApiResponse = ApiResponse<DashboardData>;
```

#### **3.2 Error Handling Types**
```typescript
interface ApiError {
  error: string;
  message: string;
  trace?: string;
  line?: number;
  file?: string;
}
```

### **Task 4: Utility Types and Guards**

#### **4.1 Type Guards**
```typescript
// Type guards for runtime validation
function isInstructor(obj: any): obj is Instructor {
  return typeof obj === 'object' && 
         typeof obj.id === 'number' &&
         typeof obj.name === 'string' &&
         typeof obj.email === 'string';
}

function isCourseDate(obj: any): obj is CourseDate {
  return typeof obj === 'object' && 
         typeof obj.id === 'number' &&
         typeof obj.course_id === 'number' &&
         typeof obj.instructor_id === 'number';
}

function isStudent(obj: any): obj is Student {
  return typeof obj === 'object' && 
         typeof obj.id === 'number' &&
         typeof obj.fname === 'string' &&
         typeof obj.lname === 'string' &&
         typeof obj.email === 'string';
}
```

#### **4.2 Utility Types**
```typescript
// Partial types for updates
type InstructorUpdate = Partial<Instructor>;
type StudentUpdate = Partial<Student>;

// Required fields only
type InstructorRequired = Pick<Instructor, 'id' | 'name' | 'email'>;
type CourseAuthRequired = Pick<CourseAuth, 'id' | 'user_id' | 'course_id'>;
```

### **Task 5: React Hook Interfaces**

#### **5.1 Hook Return Types**
```typescript
interface UseClassroomDataReturn {
  data: ClassroomData | null;
  loading: boolean;
  error: string | null;
  refetch: () => void;
}

interface UseStudentDataReturn {
  data: StudentData | null;
  loading: boolean;
  error: string | null;
  refetch: () => void;
}
```

#### **5.2 Hook Configuration Types**
```typescript
interface ClassroomDataConfig {
  endpoint: string;
  refreshInterval?: number;
  enabled?: boolean;
}

interface StudentDataConfig {
  endpoint: string;
  refreshInterval?: number;
  enabled?: boolean;
}
```

---

## 📁 FILE ORGANIZATION

### **Proposed File Structure:**
```
resources/js/types/
├── student-classroom.ts        # Main interfaces
├── api-responses.ts           # API response types
├── utility-types.ts           # Utility types and guards
└── hooks.ts                   # Hook-related types

resources/js/hooks/
├── useClassroomData.ts        # Classroom data hook
├── useStudentData.ts          # Student data hook
└── useDashboardData.ts        # Combined dashboard hook
```

---

## ✅ ACCEPTANCE CRITERIA

### **Phase 1: Core Types (2-3 hours)**
- [ ] Create `student-classroom.ts` with all core interfaces
- [ ] Include proper JSDoc comments for all interfaces
- [ ] Export all types properly
- [ ] Validate against current API responses

### **Phase 2: API Integration (2-3 hours)**
- [ ] Create API response wrapper types
- [ ] Add error handling interfaces
- [ ] Test with existing debug endpoints
- [ ] Ensure type safety in API calls

### **Phase 3: React Integration (3-4 hours)**
- [ ] Create React hook interfaces
- [ ] Implement type guards for runtime validation
- [ ] Add utility types for common operations
- [ ] Test with TanStack Query integration

### **Phase 4: Validation & Testing (2-3 hours)**
- [ ] Validate all types against actual API responses
- [ ] Create unit tests for type guards
- [ ] Document usage examples
- [ ] Update existing components to use new types

---

## 🔍 VALIDATION REQUIREMENTS

### **Runtime Validation:**
- All API responses must be validated with type guards
- Invalid data should trigger appropriate error handling
- Type mismatches should be logged for debugging

### **Development Experience:**
- Full IntelliSense support in VS Code
- Clear error messages for type violations
- Easy-to-understand interface names

### **Maintainability:**
- Types should match backend PHP model structures
- Changes to backend should be easily reflected in TypeScript
- Clear documentation for all complex types

---

## 🚀 IMPLEMENTATION ORDER

1. **Start with Core Interfaces** - Define basic data structures
2. **Add API Response Types** - Wrap core types in API response format
3. **Create Type Guards** - Runtime validation functions
4. **Implement React Hooks** - Data fetching with proper typing
5. **Test and Validate** - Ensure all types work with actual API data
6. **Documentation** - Usage examples and best practices

---

## 📝 NOTES

### **Backend Integration Points:**
- `/classroom/debug/class` - Returns ClassroomData
- `/classroom/debug/student` - Returns StudentData  
- `/classroom/debug` - Returns combined DashboardData

### **Security Considerations:**
- Student interface includes sensitive fields (password, remember_token)
- Consider creating separate interfaces for frontend vs API
- Validate what data should actually be exposed to frontend

### **Performance Considerations:**
- Large arrays of instructors/courses may need pagination types
- Consider lazy loading interfaces for detailed data
- Cache invalidation types may be needed

---

**END OF TASK SPECIFICATION**
