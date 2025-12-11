/**
 * Laravel Props Utils - Utilities for reading Laravel data from DOM
 * Provides safe access to data-* attributes set by Laravel blade templates
 */

import {
    LaravelPropsData,
    StudentDashboardData,
    ClassDashboardData,
    LaravelPropsValidator,
} from "../types/LaravelProps";

export class LaravelPropsReader {
    /**
     * Read student dashboard data from the student-props DOM element
     */
    static readStudentProps(): StudentDashboardData | null {
        try {
            console.log("🔍 Reading student props from DOM...");

            // Find the student-props element (now a script tag)
            const studentPropsElement =
                document.getElementById("student-props");
            if (!studentPropsElement) {
                console.error("❌ Student props element not found in DOM");
                return null;
            }

            console.log("✅ Student props element found:", studentPropsElement);

            // Read the text content from the script tag
            const studentDataRaw = studentPropsElement.textContent?.trim();
            if (!studentDataRaw) {
                console.error(
                    "❌ No text content found in student-props script tag"
                );
                return null;
            }

            console.log("📋 Raw student data:", studentDataRaw);

            // Parse JSON
            let studentDataParsed: any;
            try {
                studentDataParsed = JSON.parse(studentDataRaw);
                console.log("✅ Parsed student data:", studentDataParsed);
            } catch (parseError) {
                console.error(
                    "❌ Failed to parse student data JSON:",
                    parseError
                );
                console.error("❌ Raw data was:", studentDataRaw);
                return null;
            }

            // Validate the data structure
            if (
                !LaravelPropsValidator.validateStudentDashboardData(
                    studentDataParsed
                )
            ) {
                console.error("❌ Student data validation failed");
                console.error("❌ Invalid data:", studentDataParsed);

                // Return null - no fallback data (per requirement)
                console.log("❌ No fallback data - returning null");
                return null;
            }

            console.log(
                "✅ Successfully read student props:",
                studentDataParsed
            );
            return studentDataParsed as StudentDashboardData;
        } catch (error) {
            console.error("❌ Error reading student props:", error);
            return null;
        }
    }

    /**
     * Read class dashboard data from the class-props DOM element
     */
    static readClassProps(): ClassDashboardData | null {
        try {
            console.log("🔍 Reading class props from DOM...");

            // Find the class-props element
            const classPropsElement = document.getElementById("class-props");
            if (!classPropsElement) {
                console.error("❌ Class props element not found in DOM");
                return null;
            }

            console.log("✅ Class props element found:", classPropsElement);

            // Read class dashboard data
            const classDataRaw = classPropsElement.getAttribute(
                "class-dashboard-data"
            );

            if (!classDataRaw) {
                console.error("❌ class-dashboard-data attribute not found");
                console.error("❌ Element HTML:", classPropsElement.outerHTML);
                return null;
            }

            console.log("📋 Raw class data:", classDataRaw);
            console.log("📋 Raw class data length:", classDataRaw.length);
            console.log("📋 First 200 chars:", classDataRaw.substring(0, 200));

            // Parse JSON
            let classDataParsed: any;

            try {
                classDataParsed = JSON.parse(classDataRaw);
                console.log("✅ Parsed class data:", classDataParsed);
                console.log(
                    "✅ course_dates in parsed data:",
                    classDataParsed.course_dates
                );
                console.log(
                    "✅ course_dates is array:",
                    Array.isArray(classDataParsed.course_dates)
                );
                console.log(
                    "✅ course_dates length:",
                    classDataParsed.course_dates?.length
                );
            } catch (parseError) {
                console.error(
                    "❌ Failed to parse class data JSON:",
                    parseError
                );
                console.error("❌ Raw data was:", classDataRaw);
                return null;
            }

            // Validate the data structure
            if (
                !LaravelPropsValidator.validateClassDashboardData(
                    classDataParsed
                )
            ) {
                console.error("❌ Class data validation failed");
                console.error("❌ Invalid data:", classDataParsed);
                console.error(
                    "❌ Validation details - checking course_dates:",
                    {
                        hasCoursesDates: "course_dates" in classDataParsed,
                        isArray: Array.isArray(classDataParsed.course_dates),
                        length: classDataParsed.course_dates?.length,
                        value: classDataParsed.course_dates,
                    }
                );

                // Return null - no fallback data (per requirement)
                console.log("❌ No fallback data - returning null");
                return null;
            }

            console.log("✅ Successfully read class props:", classDataParsed);
            return classDataParsed as ClassDashboardData;
        } catch (error) {
            console.error("❌ Error reading class props:", error);
            return null;
        }
    }

    /**
     * Read both student and class data plus course auth ID
     */
    static readAllProps(): LaravelPropsData {
        // Get course auth ID from either element (both should have it)
        let courseAuthId: string | null = null;

        const studentPropsElement = document.getElementById("student-props");
        if (studentPropsElement) {
            courseAuthId = studentPropsElement.getAttribute(
                "data-course-auth-id"
            );
        }

        const studentData = this.readStudentProps();
        const classData = this.readClassProps();

        return {
            courseAuthId,
            studentData,
            classData,
        };
    }

    /**
     * Get student data directly from Laravel props - no fallbacks
     */
    static getStudentData(): StudentDashboardData | null {
        return this.readStudentProps();
    }

    /**
     * Get class data directly from Laravel props - no fallbacks
     */
    static getClassData(): ClassDashboardData | null {
        return this.readClassProps();
    }

    /**
     * Convert course date data to CourseDateType
     */
    static toCourseDateType(
        cd: any
    ): import("../types/classroom").CourseDateType {
        return {
            id: cd.id || 0,
            course_id: cd.course_id || 0,
            instructor_id: cd.instructor_id || 0,
            start_date: cd.start_date || cd.starts_at || "",
            end_date: cd.end_date || cd.ends_at || "",
            start_time: cd.start_time || "",
            end_time: cd.end_time || "",
            timezone: cd.timezone || "UTC",
            location: cd.location || "",
            status: cd.status || "scheduled",
            max_students: cd.max_students || 0,
            current_enrollment: cd.current_enrollment || cd.student_count || 0,
            meeting_link: cd.meeting_link || null,
            course_title: cd.course_title || cd.title || "",
            created_at: cd.created_at || "",
            updated_at: cd.updated_at || "",
        } as import("../types/classroom").CourseDateType;
    }

    /**
     * Get safe student data from Laravel props or default
     */
    static getSafeStudent(): import("../types/LaravelProps").Student {
        const studentData = this.readStudentProps();
        if (studentData?.student) {
            return studentData.student;
        }

        return {
            id: 0,
            fname: "Unknown",
            lname: "Student",
            email: "unknown@example.com",
        };
    }

    /**
     * Get safe instructor data or default
     */
    static getSafeInstructor(): import("../types/LaravelProps").Instructor {
        const classData = this.readClassProps();
        if (classData?.instructor) {
            return classData.instructor;
        }

        return {
            id: 0,
            fname: "Unknown",
            lname: "Instructor",
            email: "unknown@example.com",
        };
    }

    // =============================================================================
    // TYPE CONVERSION METHODS - Convert LaravelProps to Domain Types
    // =============================================================================

    /**
     * Convert LaravelProps.Student to StudentType
     */
    static toStudentType(
        student: import("../types/LaravelProps").Student
    ): import("../types/students.types").StudentType {
        return {
            id: student.id,
            fname: student.fname,
            lname: student.lname,
            email: student.email,
            is_active: true,
            role_id: 5, // Student role ID
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
            // Virtual properties
            name: `${student.fname} ${student.lname}`,
            fullname: `${student.fname} ${student.lname}`,
        };
    }

    /**
     * Convert LaravelProps.Instructor to InstructorType
     */
    static toInstructorType(
        instructor: import("../types/LaravelProps").Instructor
    ): import("../types/students.types").InstructorType {
        return {
            id: instructor.id,
            fname: instructor.fname,
            lname: instructor.lname,
            email: instructor.email,
            is_active: true,
            role_id: 2, // Instructor role ID
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
            // Virtual properties
            name: `${instructor.fname} ${instructor.lname}`,
            fullname: `${instructor.fname} ${instructor.lname}`,
        };
    }

    /**
     * Convert LaravelProps.CourseAuth to CourseAuthType
     */
    static toCourseAuthType(
        courseAuth: import("../types/LaravelProps").CourseAuth
    ): import("../types/students.types").CourseAuthType {
        return {
            id: courseAuth.id,
            user_id: courseAuth.user_id,
            course_id: courseAuth.course_id,
            created_at:
                typeof courseAuth.created_at === "number"
                    ? new Date(courseAuth.created_at * 1000).toISOString()
                    : courseAuth.created_at,
            updated_at:
                typeof courseAuth.updated_at === "number"
                    ? new Date(courseAuth.updated_at * 1000).toISOString()
                    : courseAuth.updated_at,
            agreed_at: courseAuth.agreed_at
                ? typeof courseAuth.agreed_at === "number"
                    ? new Date(courseAuth.agreed_at * 1000).toISOString()
                    : courseAuth.agreed_at
                : undefined,
            completed_at: courseAuth.completed_at
                ? typeof courseAuth.completed_at === "number"
                    ? new Date(courseAuth.completed_at * 1000).toISOString()
                    : courseAuth.completed_at
                : undefined,
            is_passed: courseAuth.is_passed,
            start_date: courseAuth.start_date || undefined,
            expire_date: courseAuth.expire_date || undefined,
            disabled_at: courseAuth.disabled_at
                ? typeof courseAuth.disabled_at === "number"
                    ? new Date(courseAuth.disabled_at * 1000).toISOString()
                    : courseAuth.disabled_at
                : undefined,
            disabled_reason: courseAuth.disabled_reason || undefined,
            submitted_at: courseAuth.submitted_at
                ? typeof courseAuth.submitted_at === "number"
                    ? new Date(courseAuth.submitted_at * 1000).toISOString()
                    : courseAuth.submitted_at
                : undefined,
            submitted_by: courseAuth.submitted_by || undefined,
            dol_tracking: courseAuth.dol_tracking || undefined,
            exam_admin_id: courseAuth.exam_admin_id || undefined,
            range_date_id: courseAuth.range_date_id || undefined,
            id_override: courseAuth.id_override,
            // Convert course if available
            course: courseAuth.course
                ? {
                      id: courseAuth.course.id,
                      title: courseAuth.course.title,
                      description: courseAuth.course.description,
                      price: 0,
                      total_minutes: 0,
                      policy_expire_days: 0,
                      is_active: true,
                      needs_range: false,
                  }
                : undefined,
            // Virtual properties
            progress: 0,
            status: courseAuth.completed_at
                ? "completed"
                : courseAuth.start_date
                ? "in_progress"
                : "not_started",
            is_active: !courseAuth.disabled_at,
            is_expired: false,
            is_failed: false,
        };
    }

    /**
     * Convert LaravelProps.CourseDate to CourseDateType (enhanced version)
     */
    static toCourseDate(
        courseDate: import("../types/LaravelProps").CourseDate
    ): import("../types/classroom").CourseDateType {
        return {
            id: courseDate.id,
            course_id: courseDate.course_id,
            instructor_id: 0, // Default since not in LaravelProps
            start_date: courseDate.start_date,
            end_date: courseDate.end_date,
            start_time: courseDate.session_date || courseDate.start_date,
            end_time: courseDate.end_date,
            timezone: "UTC",
            location: "Online",
            status: "scheduled",
            max_students: 20,
            current_enrollment: 0,
            meeting_link: null,
            course_title: "",
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };
    }

    /**
     * Convert arrays of LaravelProps to Domain Types
     */
    static toCourseAuthTypes(
        courseAuths: import("../types/LaravelProps").CourseAuth[]
    ): import("../types/students.types").CourseAuthType[] {
        return courseAuths.map((auth) => this.toCourseAuthType(auth));
    }

    static toCourseDateTypes(
        courseDates: import("../types/LaravelProps").CourseDate[]
    ): import("../types/classroom").CourseDateType[] {
        return courseDates.map((date) => this.toCourseDate(date));
    }

    /**
     * Get safe domain types for SchoolDashboard props
     */
    static getSafeStudentType(): import("../types/students.types").StudentType {
        const student = this.getSafeStudent();
        return this.toStudentType(student);
    }

    static getSafeInstructorType(): import("../types/students.types").InstructorType {
        const instructor = this.getSafeInstructor();
        return this.toInstructorType(instructor);
    }

    /**
     * Debug function to log all available data attributes
     */
    static debugAllDataAttributes(): void {
        console.log("🔍 Debugging all data attributes...");

        const propsElement = document.getElementById("props");
        if (!propsElement) {
            console.error("❌ Props element not found");
            return;
        }

        console.log("📋 Props element attributes:");
        for (let i = 0; i < propsElement.attributes.length; i++) {
            const attr = propsElement.attributes[i];
            console.log(`  - ${attr.name}: ${attr.value}`);
        }

        // Also check all elements with data attributes
        console.log("📋 All elements with data attributes:");
        const allDataElements = document.querySelectorAll(
            "[data-dashboard-data]"
        );
        allDataElements.forEach((element, index) => {
            console.log(`  Element ${index}:`, element);
            console.log(`    ID: ${element.id}`);
            console.log(
                `    data-dashboard-data: ${element.getAttribute(
                    "data-dashboard-data"
                )}`
            );
        });
    }
}
