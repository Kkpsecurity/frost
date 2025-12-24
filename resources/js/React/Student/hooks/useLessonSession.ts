import { useState, useEffect, useCallback } from 'react';

/**
 * Lesson Session Interface
 * Represents an active self-study lesson session
 */
export interface LessonSession {
    isActive: boolean;
    sessionId: string;  // UUID from backend
    lessonId: number;
    lessonTitle: string;
    courseAuthId: number;
    startedAt: string;  // ISO timestamp
    expiresAt: string;  // ISO timestamp
    videoDurationSeconds: number;
    totalPauseAllowed: number;  // minutes
    pauseUsed: number;  // minutes
    completionPercentage: number;  // 0-100
    playbackProgressSeconds: number;
}

/**
 * Hook Return Interface
 */
export interface UseLessonSessionReturn {
    session: LessonSession | null;
    isActive: boolean;
    isLocked: boolean;  // Other lessons locked?
    timeRemaining: number;  // Minutes until expiration
    pauseRemaining: number;  // Minutes of pause time left
    startSession: (lessonId: number, courseAuthId: number, videoDurationSeconds: number, lessonTitle: string) => Promise<{ success: boolean; error?: string; session?: LessonSession }>;
    completeSession: () => Promise<{ success: boolean; passed?: boolean; quotaConsumed?: number; error?: string }>;
    updateProgress: (playbackSeconds: number, completionPercentage: number) => void;
    trackPauseTime: (pauseMinutes: number) => void;
    terminateSession: () => void;
    refreshSession: () => void;
}

const STORAGE_KEY = 'lesson_session_active';
const COUNTDOWN_INTERVAL = 60000; // 60 seconds

/**
 * useLessonSession Hook
 * 
 * Manages self-study lesson session state with:
 * - LocalStorage persistence (survives page refresh)
 * - Session locking (only one active lesson at a time)
 * - Countdown timer (expiration tracking)
 * - Pause time tracking
 * - Progress tracking
 * - Backend API integration
 * 
 * Adapted from archived useOfflineSession pattern with enhanced features:
 * - Expiration management with auto-terminate
 * - Pause time limits
 * - Real-time progress updates
 * - Database-backed sessions via API
 * 
 * @example
 * const { session, isActive, isLocked, startSession, completeSession } = useLessonSession();
 * 
 * // Start a session
 * await startSession(123, 456, 7200, 'JavaScript Basics');
 * 
 * // Complete session
 * await completeSession();
 */
export function useLessonSession(): UseLessonSessionReturn {
    const [session, setSession] = useState<LessonSession | null>(null);
    const [timeRemaining, setTimeRemaining] = useState<number>(0);
    const [pauseRemaining, setPauseRemaining] = useState<number>(0);

    /**
     * Load session from localStorage on mount
     */
    useEffect(() => {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            try {
                const parsed: LessonSession = JSON.parse(stored);
                
                // Validate session hasn't expired
                const now = new Date();
                const expires = new Date(parsed.expiresAt);
                
                if (expires > now && parsed.isActive) {
                    setSession(parsed);
                    calculateTimeRemaining(parsed);
                } else {
                    // Expired session - clean up
                    console.log('📅 Session expired on load, cleaning up');
                    localStorage.removeItem(STORAGE_KEY);
                }
            } catch (error) {
                console.error('❌ Failed to parse stored session:', error);
                localStorage.removeItem(STORAGE_KEY);
            }
        }
    }, []);

    /**
     * Calculate time and pause remaining
     */
    const calculateTimeRemaining = useCallback((currentSession: LessonSession) => {
        const now = new Date();
        const expires = new Date(currentSession.expiresAt);
        const minutesLeft = Math.max(0, Math.floor((expires.getTime() - now.getTime()) / 60000));
        
        setTimeRemaining(minutesLeft);
        
        const pauseLeft = Math.max(0, currentSession.totalPauseAllowed - currentSession.pauseUsed);
        setPauseRemaining(pauseLeft);
    }, []);

    /**
     * Countdown timer - updates every 60 seconds
     */
    useEffect(() => {
        if (!session?.isActive) return;

        // Initial calculation
        calculateTimeRemaining(session);

        // Set up interval
        const interval = setInterval(() => {
            const now = new Date();
            const expires = new Date(session.expiresAt);
            const minutesLeft = Math.floor((expires.getTime() - now.getTime()) / 60000);
            
            setTimeRemaining(minutesLeft);

            // Auto-terminate if expired
            if (minutesLeft <= 0) {
                console.log('⏰ Session expired - auto-terminating');
                terminateSession();
                
                // Show expiration alert
                alert('Your lesson session has expired. Please start a new session to continue.');
            }

            // Show warning at 5 minutes
            if (minutesLeft === 5) {
                console.warn('⚠️ Session expiring in 5 minutes!');
                // Could trigger a toast notification here
            }
        }, COUNTDOWN_INTERVAL);

        return () => clearInterval(interval);
    }, [session, calculateTimeRemaining]);

    /**
     * Save session to localStorage
     */
    const saveSession = useCallback((sessionData: LessonSession) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(sessionData));
        setSession(sessionData);
        calculateTimeRemaining(sessionData);
    }, [calculateTimeRemaining]);

    /**
     * Start a new lesson session
     * 
     * Calls backend API to create session record and validates quota.
     * Locks all other lessons until this session is completed.
     * 
     * @param lessonId The lesson ID to start
     * @param courseAuthId The course authorization ID
     * @param videoDurationSeconds Video duration in seconds
     * @param lessonTitle Lesson title for display
     * @returns Promise with success status and session data or error
     */
    const startSession = useCallback(async (
        lessonId: number,
        courseAuthId: number,
        videoDurationSeconds: number,
        lessonTitle: string
    ): Promise<{ success: boolean; error?: string; session?: LessonSession }> => {
        try {
            console.log('🚀 Starting session:', { lessonId, courseAuthId, videoDurationSeconds });

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }

            const response = await fetch('/classroom/lesson/start-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    lesson_id: lessonId,
                    course_auth_id: courseAuthId,
                    video_duration_seconds: videoDurationSeconds,
                    lesson_title: lessonTitle,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success && data.session) {
                const newSession: LessonSession = {
                    isActive: data.session.isActive,
                    sessionId: data.session.sessionId,
                    lessonId: data.session.lessonId,
                    lessonTitle: data.session.lessonTitle,
                    courseAuthId: data.session.courseAuthId,
                    startedAt: data.session.startedAt,
                    expiresAt: data.session.expiresAt,
                    videoDurationSeconds: data.session.videoDurationSeconds,
                    totalPauseAllowed: data.session.totalPauseAllowed,
                    pauseUsed: data.session.pauseUsed,
                    completionPercentage: data.session.completionPercentage,
                    playbackProgressSeconds: data.session.playbackProgressSeconds,
                };

                saveSession(newSession);
                setSession(newSession);
                setTimeRemaining(calculateTimeRemaining(newSession.expiresAt));
                setPauseRemaining(newSession.totalPauseAllowed - newSession.pauseUsed);

                console.log('✅ Session started successfully:', newSession.sessionId);

                return { success: true, session: newSession };
            } else {
                console.error('❌ Failed to start session:', data.error);
                return { success: false, error: data.error || 'Failed to start session' };
            }
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
            console.error('❌ Error starting session:', errorMessage);
            return { 
                success: false, 
                error: errorMessage
            };
        }
    }, [saveSession]);

    /**
     * Complete the current session
     * 
     * Marks session as complete, calculates quota consumption,
     * checks if student met 80% threshold, and unlocks other lessons.
     * 
     * @returns Promise with success status, pass/fail result, and quota consumed
     */
    const completeSession = useCallback(async (): Promise<{ 
        success: boolean; 
        passed?: boolean; 
        quotaConsumed?: number; 
        error?: string 
    }> => {
        if (!session) {
            return { success: false, error: 'No active session' };
        }

        try {
            console.log('🏁 Completing session:', session.sessionId);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                throw new Error('CSRF token not found');
            }

            const response = await fetch('/classroom/lesson/complete-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    session_id: session.sessionId,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success) {
                // Clear session
                setSession(null);
                setTimeRemaining(0);
                setPauseRemaining(0);
                localStorage.removeItem(STORAGE_KEY);

                console.log('✅ Session completed:', {
                    passed: data.passed,
                    quotaConsumed: data.quota_consumed_minutes,
                });

                return {
                    success: true,
                    passed: data.passed,
                    quotaConsumed: data.quota_consumed_minutes,
                };
            } else {
                console.error('❌ Failed to complete session:', data.error);
                return { success: false, error: data.error || 'Failed to complete session' };
            }
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
            console.error('❌ Error completing session:', errorMessage);
            return {
                success: false,
                error: errorMessage,
            };
        }
    }, [session]);

    /**
     * Update video playback progress
     * 
     * Updates local state and localStorage with current playback position
     * and completion percentage. Backend sync happens separately.
     * 
     * @param playbackSeconds Current playback position in seconds
     * @param completionPercentage Percentage of video watched (0-100)
     */
    const updateProgress = useCallback((
        playbackSeconds: number,
        completionPercentage: number
    ) => {
        if (!session) return;

        const updatedSession: LessonSession = {
            ...session,
            playbackProgressSeconds: playbackSeconds,
            completionPercentage: Math.min(100, Math.max(0, completionPercentage)),
        };

        saveSession(updatedSession);
    }, [session, saveSession]);

    /**
     * Track pause time consumption
     * 
     * Increments pause time used. Called when student pauses video.
     * 
     * @param pauseMinutes Minutes to add to pause time used
     */
    const trackPauseTime = useCallback((pauseMinutes: number) => {
        if (!session) return;

        const newPauseUsed = Math.min(
            session.totalPauseAllowed,
            session.pauseUsed + pauseMinutes
        );

        const updatedSession: LessonSession = {
            ...session,
            pauseUsed: newPauseUsed,
        };

        saveSession(updatedSession);

        // Warn if pause time running low
        const remaining = session.totalPauseAllowed - newPauseUsed;
        if (remaining <= 2 && remaining > 0) {
            console.warn('⚠️ Pause time running low:', remaining, 'minutes remaining');
        }
    }, [session, saveSession]);

    /**
     * Terminate session (expired or cancelled)
     * 
     * Clears session from state and localStorage.
     * Unlocks all lessons.
     */
    const terminateSession = useCallback(() => {
        console.log('🛑 Terminating session');
        setSession(null);
        setTimeRemaining(0);
        setPauseRemaining(0);
        localStorage.removeItem(STORAGE_KEY);
    }, []);

    /**
     * Refresh session from localStorage
     * 
     * Useful for syncing state after external changes.
     */
    const refreshSession = useCallback(() => {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            try {
                const parsed: LessonSession = JSON.parse(stored);
                setSession(parsed);
                calculateTimeRemaining(parsed);
            } catch (error) {
                console.error('❌ Failed to refresh session:', error);
            }
        }
    }, [calculateTimeRemaining]);

    return {
        session,
        isActive: session?.isActive || false,
        isLocked: session?.isActive || false,
        timeRemaining,
        pauseRemaining,
        startSession,
        completeSession,
        updateProgress,
        trackPauseTime,
        terminateSession,
        refreshSession,
    };
}
