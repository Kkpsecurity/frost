/**
 * Instructor Default Dashboard Component
 * Displays the main dashboard for instructors in admin/instructor route
 */

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { queryKeys } from '../../../utils/queryConfig';

interface InstructorStats {
    totalClasses: number;
    activeStudents: number;
    completedLessons: number;
    upcomingClasses: number;
}

interface UpcomingClass {
    id: number;
    title: string;
    time: string;
    students: number;
    duration: string;
}

const InstructorDashboard: React.FC = () => {
    // Fetch instructor statistics
    const { data: stats, isLoading: statsLoading } = useQuery({
        queryKey: queryKeys.instructor.stats(),
        queryFn: async (): Promise<InstructorStats> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        totalClasses: 24,
                        activeStudents: 156,
                        completedLessons: 89,
                        upcomingClasses: 3
                    });
                }, 1000);
            });
        },
    });

    // Fetch upcoming classes
    const { data: upcomingClasses, isLoading: classesLoading } = useQuery({
        queryKey: queryKeys.instructor.upcomingClasses(),
        queryFn: async (): Promise<UpcomingClass[]> => {
            // Mock data for now - replace with actual API call
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve([
                        {
                            id: 1,
                            title: "Advanced Mathematics",
                            time: "2:00 PM",
                            students: 25,
                            duration: "60 min"
                        },
                        {
                            id: 2,
                            title: "Physics Lab Session",
                            time: "4:30 PM",
                            students: 18,
                            duration: "90 min"
                        },
                        {
                            id: 3,
                            title: "Chemistry Review",
                            time: "6:00 PM",
                            students: 32,
                            duration: "45 min"
                        }
                    ]);
                }, 800);
            });
        },
    });

    if (statsLoading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="instructor-dashboard p-6 bg-gray-50 min-h-screen">
            {/* Header */}
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-gray-800 mb-2">
                    Instructor Dashboard
                </h1>
                <p className="text-gray-600">
                    Welcome back! Here's an overview of your teaching activities.
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
                            <p className="text-sm font-medium text-gray-600">Total Classes</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.totalClasses || 0}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-green-100 text-green-600">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Active Students</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.activeStudents || 0}</p>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    <div className="flex items-center">
                        <div className="p-3 rounded-full bg-purple-100 text-purple-600">
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
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div className="ml-4">
                            <p className="text-sm font-medium text-gray-600">Upcoming Classes</p>
                            <p className="text-2xl font-bold text-gray-900">{stats?.upcomingClasses || 0}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Upcoming Classes */}
            <div className="bg-white rounded-lg shadow-md p-6">
                <h2 className="text-xl font-bold text-gray-800 mb-4">Today's Schedule</h2>

                {classesLoading ? (
                    <div className="flex justify-center py-8">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {upcomingClasses?.map((classItem) => (
                            <div key={classItem.id} className="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div className="flex items-center space-x-4">
                                    <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 className="font-semibold text-gray-800">{classItem.title}</h3>
                                        <p className="text-sm text-gray-600">
                                            {classItem.students} students • {classItem.duration}
                                        </p>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <p className="font-medium text-gray-800">{classItem.time}</p>
                                    <button className="mt-1 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                        Start Class
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Quick Actions */}
            <div className="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">Create New Class</h3>
                    <p className="text-sm text-gray-600">Start a new virtual classroom session</p>
                </button>

                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-purple-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">View Reports</h3>
                    <p className="text-sm text-gray-600">Check student progress and analytics</p>
                </button>

                <button className="p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow text-center">
                    <div className="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 className="font-semibold text-gray-800 mb-2">Schedule Class</h3>
                    <p className="text-sm text-gray-600">Plan upcoming lessons and sessions</p>
                </button>
            </div>
        </div>
    );
};

export default InstructorDashboard;
