/**
 * Student Classroom Dashboard Component
 * Displays the main dashboard for students in classroom/ route
 */

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { queryKeys } from '../../../utils/queryConfig';

interface StudentStats {
    enrolledCourses: number;
    completedLessons: number;
    assignmentsDue: number;
    hoursLearned: number;
}

interface RecentLesson {
    id: number;
    title: string;
    course: string;
    progress: number;
    duration: string;
    lastAccessed: string;
}

interface UpcomingAssignment {
    id: number;
    title: string;
    course: string;
    dueDate: string;
    type: 'quiz' | 'assignment' | 'project';
}

const StudentDashboard: React.FC = () => {
    // Fetch student statistics
    const { data: stats, isLoading: statsLoading } = useQuery({
        queryKey: queryKeys.student.stats(),
        queryFn: async (): Promise<StudentStats> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        enrolledCourses: 5,
                        completedLessons: 34,
                        assignmentsDue: 3,
                        hoursLearned: 127
                    });
                }, 1000);
            });
        },
    });

    // Fetch recent lessons
    const { data: recentLessons, isLoading: lessonsLoading } = useQuery({
        queryKey: queryKeys.student.recentLessons(),
        queryFn: async (): Promise<RecentLesson[]> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve([
                        {
                            id: 1,
                            title: "Introduction to Calculus",
                            course: "Advanced Mathematics",
                            progress: 85,
                            duration: "45 min",
                            lastAccessed: "2 hours ago"
                        },
                        {
                            id: 2,
                            title: "Chemical Bonding",
                            course: "Organic Chemistry",
                            progress: 60,
                            duration: "38 min",
                            lastAccessed: "1 day ago"
                        },
                        {
                            id: 3,
                            title: "Newton's Laws",
                            course: "Physics Fundamentals",
                            progress: 100,
                            duration: "52 min",
                            lastAccessed: "3 days ago"
                        }
                    ]);
                }, 800);
            });
        },
    });

    // Fetch upcoming assignments
    const { data: upcomingAssignments, isLoading: assignmentsLoading } = useQuery({
        queryKey: queryKeys.student.upcomingAssignments(),
        queryFn: async (): Promise<UpcomingAssignment[]> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve([
                        {
                            id: 1,
                            title: "Derivative Calculations",
                            course: "Advanced Mathematics",
                            dueDate: "Tomorrow",
                            type: "assignment"
                        },
                        {
                            id: 2,
                            title: "Molecular Structure Quiz",
                            course: "Organic Chemistry",
                            dueDate: "In 3 days",
                            type: "quiz"
                        },
                        {
                            id: 3,
                            title: "Lab Report - Motion Analysis",
                            course: "Physics Fundamentals",
                            dueDate: "Next week",
                            type: "project"
                        }
                    ]);
                }, 600);
            });
        },
    });

    const getAssignmentTypeColor = (type: string) => {
        switch (type) {
            case 'quiz': return 'bg-blue-100 text-blue-800';
            case 'assignment': return 'bg-green-100 text-green-800';
            case 'project': return 'bg-purple-100 text-purple-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    if (statsLoading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="student-dashboard p-6 bg-gray-50 min-h-screen">
            {/* Header */}
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-gray-800 mb-2">
                    Welcome to Your Classroom
                </h1>
                <p className="text-gray-600">
                    Continue your learning journey and track your progress.
                </p>
            </div>

            {/* Statistics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Enrolled Courses</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.enrolledCourses || 0}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-green-100 text-green-600">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Completed Lessons</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.completedLessons || 0}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-orange-100 text-orange-600">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Assignments Due</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.assignmentsDue || 0}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Hours Learned</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.hoursLearned || 0}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content Grid */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                {/* Recent Lessons */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h2 className="text-xl font-bold text-gray-800 mb-4">Continue Learning</h2>

                    {lessonsLoading ? (
                        <div className="flex justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {recentLessons?.map((lesson) => (
                                <div key={lesson.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div className="flex items-center justify-between mb-2">
                                        <h3 className="font-semibold text-gray-800">{lesson.title}</h3>
                                        <span className="text-sm text-gray-500">{lesson.duration}</span>
                                    </div>
                                    <p className="text-sm text-gray-600 mb-3">{lesson.course}</p>

                                    {/* Progress Bar */}
                                    <div className="mb-3">
                                        <div className="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span>{lesson.progress}%</span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-2">
                                            <div
                                                className="bg-blue-600 h-2 rounded-full"
                                                style={{ width: `${lesson.progress}%` }}
                                            ></div>
                                        </div>
                                    </div>

                                    <div className="flex justify-between items-center">
                                        <span className="text-xs text-gray-500">Last accessed {lesson.lastAccessed}</span>
                                        <button className="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                            {lesson.progress === 100 ? 'Review' : 'Continue'}
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Upcoming Assignments */}
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h2 className="text-xl font-bold text-gray-800 mb-4">Upcoming Assignments</h2>

                    {assignmentsLoading ? (
                        <div className="flex justify-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {upcomingAssignments?.map((assignment) => (
                                <div key={assignment.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <h3 className="font-semibold text-gray-800 mb-1">{assignment.title}</h3>
                                            <p className="text-sm text-gray-600 mb-2">{assignment.course}</p>
                                            <div className="flex items-center space-x-2">
                                                <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getAssignmentTypeColor(assignment.type)}`}>
                                                    {assignment.type}
                                                </span>
                                                <span className="text-sm text-gray-500">Due: {assignment.dueDate}</span>
                                            </div>
                                        </div>
                                        <button className="ml-4 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors">
                                            Start
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {/* Quick Actions */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">Browse Courses</h3>
                    <p className="text-sm text-gray-600">Explore available courses</p>
                </button>

                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">My Progress</h3>
                    <p className="text-sm text-gray-600">View detailed progress reports</p>
                </button>

                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-purple-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">Schedule</h3>
                    <p className="text-sm text-gray-600">View class schedule and events</p>
                </button>

                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-orange-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">Get Help</h3>
                    <p className="text-sm text-gray-600">Contact support or get assistance</p>
                </button>
            </div>
        </div>
    );
};

export default StudentDashboard;
