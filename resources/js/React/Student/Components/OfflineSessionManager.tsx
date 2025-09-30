/**
 * OfflineSessionManager Component
 *
 * Example React component demonstrating offline session tracking usage
 */

import React, { useState, useEffect } from 'react';
import { useOfflineSessionTracking, OfflineStepKey } from '../hooks/useOfflineSessionTracking';

interface OfflineSessionManagerProps {
    courseAuthId: number;
    autoStart?: boolean;
}

export const OfflineSessionManager: React.FC<OfflineSessionManagerProps> = ({
    courseAuthId,
    autoStart = false
}) => {

    // Use the tracking hook
    const {
        isActive,
        sessionId,
        currentStep,
        sessionStatus,
        isLoading,
        error,
        sessionDuration,
        startSession,
        endSession,
        trackStep,
        startLesson,
        completeLesson,
        refreshStatus,
        sessionSteps,
        stepOrder,
    } = useOfflineSessionTracking({
        courseAuthId,
        autoStart,
        autoTrackSteps: true,
    });

    // Local state for demo purposes
    const [selectedLessonId, setSelectedLessonId] = useState<number>(1);
    const [activeLessons, setActiveLessons] = useState<Set<number>>(new Set());

    // =============================================================================
    // EVENT HANDLERS
    // =============================================================================

    const handleStartSession = async () => {
        try {
            await startSession();
        } catch (error) {
            console.error('Failed to start session:', error);
        }
    };

    const handleEndSession = async () => {
        try {
            await endSession();
            setActiveLessons(new Set()); // Clear active lessons
        } catch (error) {
            console.error('Failed to end session:', error);
        }
    };

    const handleStepClick = async (stepKey: OfflineStepKey) => {
        try {
            await trackStep(stepKey, {
                triggered_by: 'user_click',
                user_interface: 'session_manager_component',
            });
        } catch (error) {
            console.error(`Failed to track step ${stepKey}:`, error);
        }
    };

    const handleStartLesson = async (lessonId: number) => {
        try {
            await startLesson(lessonId, `Lesson ${lessonId}`);
            setActiveLessons(prev => new Set(prev).add(lessonId));
        } catch (error) {
            console.error(`Failed to start lesson ${lessonId}:`, error);
        }
    };

    const handleCompleteLesson = async (lessonId: number) => {
        try {
            await completeLesson(lessonId, [`section_1`, `section_2`]);
            setActiveLessons(prev => {
                const newSet = new Set(prev);
                newSet.delete(lessonId);
                return newSet;
            });
        } catch (error) {
            console.error(`Failed to complete lesson ${lessonId}:`, error);
        }
    };

    // =============================================================================
    // RENDER HELPERS
    // =============================================================================

    const formatDuration = (seconds: number): string => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (hours > 0) {
            return `${hours}h ${minutes}m ${secs}s`;
        } else if (minutes > 0) {
            return `${minutes}m ${secs}s`;
        } else {
            return `${secs}s`;
        }
    };

    const getStepClassName = (stepKey: OfflineStepKey): string => {
        const baseClass = 'step-item px-3 py-2 rounded cursor-pointer transition-colors';

        if (stepKey === currentStep) {
            return `${baseClass} bg-blue-500 text-white`;
        }

        const currentIndex = stepOrder.indexOf(currentStep || 'view_lessons');
        const stepIndex = stepOrder.indexOf(stepKey);

        if (stepIndex < currentIndex) {
            return `${baseClass} bg-green-200 text-green-800`;
        } else if (stepIndex === currentIndex) {
            return `${baseClass} bg-blue-200 text-blue-800`;
        } else {
            return `${baseClass} bg-gray-200 text-gray-600`;
        }
    };

    // =============================================================================
    // RENDER
    // =============================================================================

    if (isLoading) {
        return (
            <div className="flex items-center justify-center p-8">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span className="ml-2">Loading session...</span>
            </div>
        );
    }

    return (
        <div className="offline-session-manager p-6 bg-white rounded-lg shadow-lg">
            {/* Session Status Header */}
            <div className="mb-6">
                <h2 className="text-2xl font-bold mb-4">Offline Study Session</h2>

                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center space-x-4">
                        <div className={`status-indicator w-3 h-3 rounded-full ${
                            isActive ? 'bg-green-500' : 'bg-gray-400'
                        }`}></div>
                        <span className="font-medium">
                            Status: {isActive ? 'Active' : 'Inactive'}
                        </span>
                        {sessionId && (
                            <span className="text-sm text-gray-600">
                                Session ID: {sessionId}
                            </span>
                        )}
                    </div>

                    <div className="text-right">
                        <div className="text-lg font-mono">
                            {formatDuration(sessionDuration)}
                        </div>
                        <div className="text-sm text-gray-600">Duration</div>
                    </div>
                </div>

                {/* Session Controls */}
                <div className="flex space-x-3">
                    {!isActive ? (
                        <button
                            onClick={handleStartSession}
                            className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                        >
                            Start Session
                        </button>
                    ) : (
                        <button
                            onClick={handleEndSession}
                            className="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                        >
                            End Session
                        </button>
                    )}

                    <button
                        onClick={refreshStatus}
                        className="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors"
                    >
                        Refresh Status
                    </button>
                </div>
            </div>

            {/* Error Display */}
            {error && (
                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    Error: {error}
                </div>
            )}

            {/* Session Steps Progress */}
            <div className="mb-6">
                <h3 className="text-lg font-semibold mb-3">Session Steps</h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                    {stepOrder.map((stepKey) => (
                        <div
                            key={stepKey}
                            onClick={() => handleStepClick(stepKey)}
                            className={getStepClassName(stepKey)}
                        >
                            <div className="text-sm font-medium">
                                {sessionSteps[stepKey]}
                            </div>
                        </div>
                    ))}
                </div>

                {currentStep && (
                    <div className="mt-2 text-sm text-gray-600">
                        Current Step: <span className="font-medium">{sessionSteps[currentStep]}</span>
                    </div>
                )}
            </div>

            {/* Lesson Tracking Demo */}
            {isActive && (
                <div className="mb-6">
                    <h3 className="text-lg font-semibold mb-3">Lesson Tracking</h3>

                    <div className="flex items-center space-x-3 mb-3">
                        <select
                            value={selectedLessonId}
                            onChange={(e) => setSelectedLessonId(parseInt(e.target.value))}
                            className="border rounded px-3 py-2"
                        >
                            {[1, 2, 3, 4, 5].map(id => (
                                <option key={id} value={id}>Lesson {id}</option>
                            ))}
                        </select>

                        {!activeLessons.has(selectedLessonId) ? (
                            <button
                                onClick={() => handleStartLesson(selectedLessonId)}
                                className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
                            >
                                Start Lesson
                            </button>
                        ) : (
                            <button
                                onClick={() => handleCompleteLesson(selectedLessonId)}
                                className="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors"
                            >
                                Complete Lesson
                            </button>
                        )}
                    </div>

                    {activeLessons.size > 0 && (
                        <div className="text-sm text-gray-600">
                            Active Lessons: {Array.from(activeLessons).join(', ')}
                        </div>
                    )}
                </div>
            )}

            {/* Session Status Details */}
            {sessionStatus && (
                <div className="bg-gray-50 p-4 rounded">
                    <h3 className="text-lg font-semibold mb-2">Session Details</h3>
                    <div className="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Activities Count:</strong> {sessionStatus.activities_count}
                        </div>
                        <div>
                            <strong>Lessons Accessed:</strong> {sessionStatus.lessons_accessed}
                        </div>
                    </div>

                    {sessionStatus.recent_activities.length > 0 && (
                        <div className="mt-3">
                            <strong className="text-sm">Recent Activities:</strong>
                            <ul className="text-sm mt-1">
                                {sessionStatus.recent_activities.slice(0, 3).map((activity, index) => (
                                    <li key={index} className="text-gray-600">
                                        {activity.type} - {activity.timestamp}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default OfflineSessionManager;
