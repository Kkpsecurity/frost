/**
 * useOfflineSessionTracking Hook
 *
 * React hook for managing offline session tracking with session steps integration
 */

import { useState, useEffect, useCallback, useRef } from 'react';
import { offlineSessionTracking, SessionStatus } from '../services/offlineSessionTracking';

// Session steps configuration (matching your provided config)
const sessionStepsConfig = {
    offline: {
        view_lessons: "View Lessons",
        begin_session: "Begin Session",
        student_agreement: "Student Agreement",
        student_rules: "Student Rules",
        student_verification: "Student Verification",
        active_session: "Active Session",
        completed_session: "Completed Session",
    },
};

export type OfflineStepKey = keyof typeof sessionStepsConfig.offline;

export interface UseOfflineSessionTrackingProps {
    courseAuthId: number;
    autoStart?: boolean;
    autoTrackSteps?: boolean;
}

export interface OfflineSessionState {
    isActive: boolean;
    sessionId: number | null;
    currentStep: OfflineStepKey | null;
    sessionStatus: SessionStatus | null;
    isLoading: boolean;
    error: string | null;
}

export const useOfflineSessionTracking = ({
    courseAuthId,
    autoStart = false,
    autoTrackSteps = true,
}: UseOfflineSessionTrackingProps) => {

    // State management
    const [sessionState, setSessionState] = useState<OfflineSessionState>({
        isActive: false,
        sessionId: null,
        currentStep: null,
        sessionStatus: null,
        isLoading: false,
        error: null,
    });

    // Refs for tracking
    const sessionStartTimeRef = useRef<number | null>(null);
    const lessonStartTimeRef = useRef<Record<number, number>>({});
    const stepTimestampsRef = useRef<Record<OfflineStepKey, number>>({});

    // =============================================================================
    // SESSION MANAGEMENT
    // =============================================================================

    /**
     * Start offline session
     */
    const startSession = useCallback(async () => {
        if (sessionState.isActive) {
            console.warn('Session already active');
            return;
        }

        setSessionState(prev => ({ ...prev, isLoading: true, error: null }));

        try {
            const sessionData = await offlineSessionTracking.autoStartSession(courseAuthId);
            sessionStartTimeRef.current = Date.now();

            setSessionState(prev => ({
                ...prev,
                isActive: true,
                sessionId: sessionData.session_id,
                currentStep: 'begin_session',
                isLoading: false,
            }));

            // Auto-track begin_session step if enabled
            if (autoTrackSteps) {
                await trackStep('begin_session');
            }

            console.log('Offline session started:', sessionData);
            return sessionData;

        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to start session';
            setSessionState(prev => ({
                ...prev,
                isLoading: false,
                error: errorMessage,
            }));
            console.error('Failed to start offline session:', error);
            throw error;
        }
    }, [courseAuthId, sessionState.isActive, autoTrackSteps]);

    /**
     * End offline session
     */
    const endSession = useCallback(async () => {
        if (!sessionState.isActive) {
            console.warn('No active session to end');
            return;
        }

        setSessionState(prev => ({ ...prev, isLoading: true, error: null }));

        try {
            // Track completed_session step if enabled
            if (autoTrackSteps) {
                await trackStep('completed_session');
            }

            const sessionData = await offlineSessionTracking.endSession(courseAuthId);
            sessionStartTimeRef.current = null;
            lessonStartTimeRef.current = {};
            stepTimestampsRef.current = {};

            setSessionState(prev => ({
                ...prev,
                isActive: false,
                sessionId: null,
                currentStep: null,
                isLoading: false,
            }));

            console.log('Offline session ended:', sessionData);
            return sessionData;

        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to end session';
            setSessionState(prev => ({
                ...prev,
                isLoading: false,
                error: errorMessage,
            }));
            console.error('Failed to end offline session:', error);
            throw error;
        }
    }, [courseAuthId, sessionState.isActive, autoTrackSteps]);

    // =============================================================================
    // STEP TRACKING
    // =============================================================================

    /**
     * Track session step progression
     */
    const trackStep = useCallback(async (stepKey: OfflineStepKey, additionalData?: any) => {
        try {
            const stepLabel = sessionStepsConfig.offline[stepKey];
            const previousStep = sessionState.currentStep;
            const stepStartTime = Date.now();

            // Calculate step duration if we have previous step timing
            let stepDuration: number | undefined;
            if (previousStep && stepTimestampsRef.current[previousStep]) {
                stepDuration = stepStartTime - stepTimestampsRef.current[previousStep];
            }

            const stepData = {
                step_duration: stepDuration,
                previous_step: previousStep,
                session_duration: sessionStartTimeRef.current
                    ? stepStartTime - sessionStartTimeRef.current
                    : undefined,
                ...additionalData,
            };

            const result = await offlineSessionTracking.trackSessionStep(
                courseAuthId,
                stepKey,
                stepLabel,
                stepData
            );

            // Update state and timing
            stepTimestampsRef.current[stepKey] = stepStartTime;
            setSessionState(prev => ({
                ...prev,
                currentStep: stepKey,
                error: null,
            }));

            console.log(`Step tracked: ${stepKey} (${stepLabel})`, result);
            return result;

        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to track step';
            setSessionState(prev => ({ ...prev, error: errorMessage }));
            console.error(`Failed to track step ${stepKey}:`, error);
            throw error;
        }
    }, [courseAuthId, sessionState.currentStep]);

    // =============================================================================
    // LESSON TRACKING
    // =============================================================================

    /**
     * Start lesson tracking
     */
    const startLesson = useCallback(async (lessonId: number, lessonTitle?: string) => {
        try {
            lessonStartTimeRef.current[lessonId] = Date.now();

            const result = await offlineSessionTracking.startLesson(
                courseAuthId,
                lessonId,
                lessonTitle
            );

            console.log(`Lesson ${lessonId} started:`, result);
            return result;

        } catch (error) {
            console.error(`Failed to start lesson ${lessonId}:`, error);
            throw error;
        }
    }, [courseAuthId]);

    /**
     * Complete lesson tracking
     */
    const completeLesson = useCallback(async (
        lessonId: number,
        completedSections?: string[]
    ) => {
        try {
            const lessonStartTime = lessonStartTimeRef.current[lessonId];
            const timeSpent = lessonStartTime ? Date.now() - lessonStartTime : 0;

            const result = await offlineSessionTracking.completeLesson(
                courseAuthId,
                lessonId,
                Math.floor(timeSpent / 1000), // Convert to seconds
                completedSections
            );

            // Clean up lesson timing
            delete lessonStartTimeRef.current[lessonId];

            console.log(`Lesson ${lessonId} completed:`, result);
            return result;

        } catch (error) {
            console.error(`Failed to complete lesson ${lessonId}:`, error);
            throw error;
        }
    }, [courseAuthId]);

    // =============================================================================
    // STATUS & UTILITIES
    // =============================================================================

    /**
     * Refresh session status
     */
    const refreshStatus = useCallback(async () => {
        try {
            const status = await offlineSessionTracking.getSessionStatus(courseAuthId);

            setSessionState(prev => ({
                ...prev,
                sessionStatus: status,
                isActive: status.has_active_session,
                sessionId: status.session_id,
            }));

            return status;

        } catch (error) {
            console.error('Failed to refresh session status:', error);
            setSessionState(prev => ({
                ...prev,
                error: error instanceof Error ? error.message : 'Failed to refresh status',
            }));
            throw error;
        }
    }, [courseAuthId]);

    /**
     * Get session analytics
     */
    const getSessionSummary = useCallback(async (dateFrom?: string, dateTo?: string) => {
        try {
            return await offlineSessionTracking.getSessionSummary(courseAuthId, dateFrom, dateTo);
        } catch (error) {
            console.error('Failed to get session summary:', error);
            throw error;
        }
    }, [courseAuthId]);

    // =============================================================================
    // EFFECTS
    // =============================================================================

    /**
     * Initialize session status on mount
     */
    useEffect(() => {
        refreshStatus();
    }, [refreshStatus]);

    /**
     * Auto-start session if enabled
     */
    useEffect(() => {
        if (autoStart && !sessionState.isActive && !sessionState.isLoading) {
            startSession();
        }
    }, [autoStart, sessionState.isActive, sessionState.isLoading, startSession]);

    /**
     * Cleanup on unmount
     */
    useEffect(() => {
        return () => {
            // Optional: Auto-end session on unmount
            // This might not be desired behavior, so it's commented out
            // if (sessionState.isActive) {
            //     endSession();
            // }
        };
    }, []);

    // =============================================================================
    // RETURN INTERFACE
    // =============================================================================

    return {
        // State
        ...sessionState,

        // Session management
        startSession,
        endSession,
        refreshStatus,

        // Step tracking
        trackStep,

        // Lesson tracking
        startLesson,
        completeLesson,

        // Analytics
        getSessionSummary,

        // Utilities
        sessionSteps: sessionStepsConfig.offline,
        stepOrder: Object.keys(sessionStepsConfig.offline) as OfflineStepKey[],

        // Computed properties
        sessionDuration: sessionStartTimeRef.current
            ? Math.floor((Date.now() - sessionStartTimeRef.current) / 1000)
            : 0,
    };
};
