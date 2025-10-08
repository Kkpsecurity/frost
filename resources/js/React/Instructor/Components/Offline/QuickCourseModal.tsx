import React, { useState, useEffect } from "react";

interface Course {
    id: number;
    title: string;
}

interface Instructor {
    id: number;
    fname: string;
    lname: string;
}

interface QuickCourseModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
}

const QuickCourseModal: React.FC<QuickCourseModalProps> = ({
    isOpen,
    onClose,
    onSuccess,
}) => {
    const [courses, setCourses] = useState<Course[]>([]);
    const [instructors, setInstructors] = useState<Instructor[]>([]);
    const [selectedCourse, setSelectedCourse] = useState<string>("");
    const [selectedInstructor, setSelectedInstructor] = useState<string>("");
    const [loading, setLoading] = useState<boolean>(false);
    const [creating, setCreating] = useState<boolean>(false);

    // Load courses and instructors when modal opens
    useEffect(() => {
        if (isOpen && courses.length === 0) {
            loadData();
        }
    }, [isOpen]);

    const loadData = async () => {
        setLoading(true);
        try {
            // Check if user is sys_admin first
            const userResponse = await fetch('/admin/instructors/validate', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (userResponse.ok) {
                const userData = await userResponse.json();
                if (!userData?.instructor?.is_sys_admin) {
                    throw new Error('Access denied. Sys admin privileges required.');
                }
            }

            // Load courses and instructors in parallel
            const [coursesResponse, instructorsResponse] = await Promise.all([
                fetch('/admin/course-dates/data/courses', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }),
                fetch('/admin/course-dates/data/instructors', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }),
            ]);

            if (coursesResponse.ok) {
                const coursesData = await coursesResponse.json();
                setCourses(coursesData.data || []);
            }

            if (instructorsResponse.ok) {
                const instructorsData = await instructorsResponse.json();
                setInstructors(instructorsData.data || []);
            }
        } catch (error) {
            console.error('Error loading data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!selectedCourse) {
            alert('Please select a course');
            return;
        }

        setCreating(true);

        try {
            const response = await fetch('/admin/course-dates/generator/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    course_id: selectedCourse,
                    instructor_id: selectedInstructor || null,
                }),
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                if ((window as any).toastr) {
                    (window as any).toastr.success(result.message || 'Test course created successfully!');
                } else {
                    alert('Test course created successfully!');
                }

                // Reset form and close modal
                setSelectedCourse("");
                setSelectedInstructor("");
                onClose();
                onSuccess(); // Refresh the dashboard
            } else {
                throw new Error(result.message || 'Failed to create course');
            }
        } catch (error) {
            console.error('Error creating course:', error);
            if ((window as any).toastr) {
                (window as any).toastr.error(error instanceof Error ? error.message : 'Failed to create test course');
            } else {
                alert('Failed to create test course: ' + (error instanceof Error ? error.message : 'Unknown error'));
            }
        } finally {
            setCreating(false);
        }
    };

    const handleClose = () => {
        if (!creating) {
            setSelectedCourse("");
            setSelectedInstructor("");
            onClose();
        }
    };

    if (!isOpen) return null;

    return (
        <div className="modal fade show" style={{ display: 'block', backgroundColor: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">
                            <i className="fas fa-plus-circle mr-2"></i>
                            Create Test Course for Today
                        </h5>
                        <button
                            type="button"
                            className="close"
                            onClick={handleClose}
                            disabled={creating}
                        >
                            <span>&times;</span>
                        </button>
                    </div>

                    <form onSubmit={handleSubmit}>
                        <div className="modal-body">
                            <div className="alert alert-info">
                                <i className="fas fa-info-circle mr-2"></i>
                                This will create a test course session for today using the course's template times.
                            </div>

                            {loading ? (
                                <div className="text-center">
                                    <i className="fas fa-spinner fa-spin mr-2"></i>
                                    Loading courses...
                                </div>
                            ) : (
                                <>
                                    <div className="form-group">
                                        <label htmlFor="course-select">
                                            Select Course <span className="text-danger">*</span>
                                        </label>
                                        <select
                                            id="course-select"
                                            className="form-control"
                                            value={selectedCourse}
                                            onChange={(e) => setSelectedCourse(e.target.value)}
                                            required
                                            disabled={creating}
                                        >
                                            <option value="">Choose a course...</option>
                                            {courses.map((course) => (
                                                <option key={course.id} value={course.id}>
                                                    {course.title}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="form-group">
                                        <label htmlFor="instructor-select">
                                            Assign Instructor (Optional)
                                        </label>
                                        <select
                                            id="instructor-select"
                                            className="form-control"
                                            value={selectedInstructor}
                                            onChange={(e) => setSelectedInstructor(e.target.value)}
                                            disabled={creating}
                                        >
                                            <option value="">No instructor assigned</option>
                                            {instructors.map((instructor) => (
                                                <option key={instructor.id} value={instructor.id}>
                                                    {instructor.fname} {instructor.lname}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="bg-light p-3 rounded">
                                        <small className="text-muted">
                                            <strong>Date:</strong> {new Date().toLocaleDateString('en-US', {
                                                weekday: 'long',
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric'
                                            })} (Today)<br/>
                                            <strong>Times:</strong> Will use the selected course's template times
                                        </small>
                                    </div>
                                </>
                            )}
                        </div>

                        <div className="modal-footer">
                            <button
                                type="button"
                                className="btn btn-secondary"
                                onClick={handleClose}
                                disabled={creating}
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                className="btn btn-success"
                                disabled={loading || creating || !selectedCourse}
                            >
                                {creating ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                        Creating...
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-plus-circle mr-2"></i>
                                        Create Test Course
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default QuickCourseModal;
